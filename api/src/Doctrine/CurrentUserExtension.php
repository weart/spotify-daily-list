<?php

namespace App\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Membership;
use App\Entity\Organization;
use App\Entity\Poll;
use App\Entity\Session;
use App\Entity\Track;
use App\Entity\User;
use App\Entity\Vote;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\Security;

final class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = [])
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }
        switch ($resourceClass) {
            case Membership::class:
                $this->addWhereMembership($queryBuilder);break;
            case Organization::class:
                $this->addWhereOrganization($queryBuilder);break;
            case Poll::class:
                $this->addWherePoll($queryBuilder);break;
            case Session::class:
                $this->addWhereSession($queryBuilder);break;
            case Track::class:
                $this->addWhereTrack($queryBuilder);break;
            case User::class:
                $this->addWhereUser($queryBuilder);break;
            case Vote::class:
                $this->addWhereVote($queryBuilder);break;
            default:
                throw new \Exception('Invalid resource');break;
        }
    }

    //In security.yaml: IS_AUTHENTICATED_FULLY
    private function addWhereMembership(QueryBuilder $queryBuilder): void
    {
        //self
        $this->addWhereCurrentUser($queryBuilder, 'member');

        //organization.public_membership (anyone can be member, but the list of members is private)
//        $rootAlias = $queryBuilder->getRootAliases()[0];
//        $queryBuilder->innerJoin($rootAlias.'.organization', 'org');
//        $queryBuilder->innerJoin($rootAlias.'.organization', 'org', Join::WITH,
//            $queryBuilder->expr()->eq('org.publicMembership', ':publicMembership')
//        );
//        $queryBuilder->orWhere(
//            $queryBuilder->expr()->eq('org.publicMembership', ':publicMembership')
//        );
//        $queryBuilder->setParameter('publicMembership', true);

        //self.organization.membership.roles > 1
    }

    private function addWhereOrganization(QueryBuilder $queryBuilder): void
    {
        //self: organization.membership.member_id
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->innerJoin($rootAlias.'.memberships', 'mem');
        $queryBuilder->where($queryBuilder->expr()->eq('mem.member', ':member'));
        $queryBuilder->setParameter('member', $this->getCurrentUserUuid());

        //publicVisibility
        $queryBuilder->orWhere($rootAlias.'.publicVisibility = :public_visibility');
        $queryBuilder->setParameter('public_visibility', true);
    }

    private function addWherePoll(QueryBuilder $queryBuilder): void
    {
        //self: poll.organization.membership.member_id
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->innerJoin($rootAlias.'.organization', 'org');
        $queryBuilder->leftJoin('org.memberships', 'mem', Join::WITH,
            $queryBuilder->expr()->lte('mem.roles', Membership::MEMBER)
        );
        $queryBuilder->where($queryBuilder->expr()->eq('mem.member', ':member'));
        $queryBuilder->setParameter('member', $this->getCurrentUserUuid());

        //publicVisibility
        $queryBuilder->orWhere($rootAlias.'.publicVisibility = :public_visibility');
        $queryBuilder->setParameter('public_visibility', true);
    }

    //In security.yaml: IS_AUTHENTICATED_FULLY
    private function addWhereSession(QueryBuilder $queryBuilder): void
    {
        $this->addWhereCurrentUser($queryBuilder, 'user_id');
    }

    private function addWhereTrack(QueryBuilder $queryBuilder): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->innerJoin($rootAlias.'.poll', 'poll');
        $queryBuilder->innerJoin('poll.organization', 'org');
        $queryBuilder->innerJoin('org.memberships', 'mem', Join::WITH,
            $queryBuilder->expr()->lte('mem.roles', Membership::MEMBER)
        );

        $queryBuilder->where($queryBuilder->expr()->orX(
        //track.poll.public_visibility
            $queryBuilder->expr()->eq('poll.publicVisibility', ':public_visibility'),
        //track.poll.organization.membership.member_id
            $queryBuilder->expr()->eq('mem.member', ':member')
        ));
        $queryBuilder->setParameter('public_visibility', true);
        $queryBuilder->setParameter('member', $this->getCurrentUserUuid());
    }

    //In security.yaml: IS_AUTHENTICATED_FULLY
    private function addWhereUser(QueryBuilder $queryBuilder): void
    {
        //self OR (enabled AND public_visibility)
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->where($queryBuilder->expr()->orX(
            $queryBuilder->expr()->eq($rootAlias.'.id', ':current_user'),
            $queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq($rootAlias.'.enabled', ':enabled'),
                $queryBuilder->expr()->eq($rootAlias.'.publicVisibility', ':public_visibility')
            )
        ));

        $queryBuilder->setParameter('enabled', true);
        $queryBuilder->setParameter('public_visibility', true);
        $queryBuilder->setParameter('current_user', $this->getCurrentUserUuid());
    }

    private function addWhereVote(QueryBuilder $queryBuilder): void
    {
        //self (session or user)
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->where($queryBuilder->expr()->orX(
            $queryBuilder->where($rootAlias.'.user = :current_user'),
            $queryBuilder->where($rootAlias.'.session = :current_session')
        ));
        $queryBuilder->setParameter('current_user', $this->getCurrentUserUuid());
        $queryBuilder->setParameter('current_session', $this->getCurrentUser()->getSession());

        //poll.public_votes?
    }

    /*
     * Helpers
     */

    private function addWhereCurrentUser(QueryBuilder $queryBuilder, string $field): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->where(sprintf('%s.%s = :current_user', $rootAlias, $field));
        $queryBuilder->setParameter('current_user', $this->getCurrentUserUuid());
    }

    private function getCurrentUser(): User
    {
        if (null === ($user = $this->security->getUser())) {
            $msg = 'Authentication credentials could not be found.'; //msg from getMessageKey function
            throw new AuthenticationCredentialsNotFoundException($msg);
        }
        return $user;
    }

    private function getCurrentUserUuid(): string
    {
        $user = $this->getCurrentUser();
        return $user->getId()->toString();
    }

//    private function printQuery(QueryBuilder $queryBuilder): void
//    {
//        $query = $queryBuilder->getQuery();
//        var_dump($query->getSQL(), $query->getParameters());
//        return;
//        $vals = $query->getFlattenedParams();
//        $sql = $query->getDql();
//        $sql = str_replace('?', '%s', $sql);
//        var_dump(vsprintf($sql, $vals));
//    }
}
