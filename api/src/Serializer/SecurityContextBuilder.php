<?php
// api/src/Serializer/BookContextBuilder.php

namespace App\Serializer;

use ApiPlatform\Core\Api\OperationType;
use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use App\Entity\Membership;
use App\Entity\Organization;
use App\Entity\Poll;
use App\Entity\Session;
use App\Entity\Track;
use App\Entity\User;
use App\Entity\Vote;

final class SecurityContextBuilder implements SerializerContextBuilderInterface
{
    const admin_key = 'admin';
    const member_key = 'member';
    const self_key = 'self';
    const anon_key = 'anon';

    const membership_key = 'member';
    const organization_key = 'org';
    const poll_key = 'poll';
    const session_key = 'session';
    const track_key = 'track';
    const user_key = 'user';
    const vote_key = 'vote';

    const collection_get_key = 'list';
    const collection_post_key = 'create';
    const item_get_key = 'get';
    const item_put_key = 'replace';
    const item_patch_key = 'update';
    const item_delete_key = 'delete';

    private $decorated;
    private $authorizationChecker;
    private $logger;

    public function __construct(
        SerializerContextBuilderInterface $decorated,
        AuthorizationCheckerInterface $authorizationChecker,
        LoggerInterface $logger
    ) {
        $this->decorated = $decorated;
        $this->authorizationChecker = $authorizationChecker;
        $this->logger = $logger;
    }

    /**
     * Add new group based on three states: Authorization, Resource name, Operation
     *
     * @param Request $request
     * @param bool $normalization
     * @param array|null $extractedAttributes
     * @return array
     * @throws \Exception
     */
    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
//        print_r('ROLE:');
//        var_dump($this->authorizationChecker->isGranted('ROLE_ADMIN'));
//        var_dump($this->authorizationChecker->isGranted('ROLE_USER'));
//        print_r($this->authorizationChecker->isGranted('ROLE_USER'));
//        exit;
//        object.owner

//        $resourceClass = $context['resource_class'] ?? null;
//        if ($resourceClass === Book::class && isset($context['groups']) && $this->authorizationChecker->isGranted('ROLE_ADMIN') && false === $normalization) {
//            $context['groups'][] = 'admin:input';
//        }

        if (empty($context['resource_class'])) {
//            return $context;
            throw new \Exception('Invalid resource class');
        }

        $roles = $this->getAuthorizationContext($context);
        foreach ($roles as $role) {
            $tmp_context = sprintf('%s:%s:%s',
                $role,
                $this->getResourceContext($context),
                $this->getOperationContext($context)
            );

            $this->logger->info('security group added', [
                $tmp_context,
                $context['groups']
            ]);

            $context['groups'][] = $tmp_context;
        }

        return $context;
    }

    private function getAuthorizationContext($context) {
        $roles = [self::anon_key];
//        var_dump($this->authorizationChecker->isGranted(User::ADMIN));
//        var_dump($this->authorizationChecker->isGranted(User::MEMBER));
//        exit;

        if ($this->authorizationChecker->isGranted(User::ADMIN)) {
            $roles[] = self::admin_key;
        }
        if ($this->authorizationChecker->isGranted(User::MEMBER)) {
            $roles[] = self::member_key;
        }
//        if (object.owner == user) {
//            $roles[] = self::self_key;
//        }
        return $roles;
    }

    private function getResourceContext($context) {
        switch ($context['resource_class']) {
            case Membership::class:
                return self::membership_key;break;
            case Organization::class:
                return self::organization_key;break;
            case Poll::class:
                return self::poll_key;break;
            case Session::class:
                return self::session_key;break;
            case Track::class:
                return self::track_key;break;
            case User::class:
                return self::user_key;break;
            case Vote::class:
                return self::vote_key;break;
            default:
                throw new \Exception('Invalid resource');break;
        }
    }

    private function getOperationContext($context) {
        if ($context['operation_type'] === OperationType::COLLECTION) {
            if ($context['collection_operation_name'] === 'get') {
                return self::collection_get_key;
            } else if ($context['collection_operation_name'] === 'post') {
                return self::collection_post_key;
            } else {
                throw new \Exception('Invalid collection operation name');
            }
        } else if ($context['operation_type'] === OperationType::ITEM) {
            if ($context['item_operation_name'] === 'get') {
                return self::item_get_key;
            } else if ($context['item_operation_name'] === 'put') {
                return self::item_put_key;
            } else if ($context['item_operation_name'] === 'patch') {
                return self::item_patch_key;
            } else if ($context['item_operation_name'] === 'delete') {
                return self::item_delete_key;
            } else {
                throw new \Exception('Invalid item operation name');
            }
        } else {
            throw new \Exception('Invalid operation type');
        }
    }
}
