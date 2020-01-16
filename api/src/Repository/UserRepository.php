<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use Doctrine\Common\Collections\Criteria;
//use Doctrine\ORM\EntityManager;
//use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class UserRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @return EntityManager
     *
    public function getEm(): EntityManager
    {
        return $this->_em;
    }
    public function getSpotifyListOwner(): User
    {
        return $this->getEm()->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.spotify_list_owner = :owner')
            ->setParameter('owner', true)
            ->getQuery()->getSingleResult();
    }

    public function getSpotifyCredentials(): array
    {
        $owner = $this->getSpotifyListOwner();
        return [
            $owner->getAccessToken(),
            $owner->getRefreshToken()
        ];
    }

    public function saveSpotifyCredentials(string $accessToken, string $refreshToken): void
    {
        $owner = $this->getSpotifyListOwner();

        $owner->setAccessToken($accessToken);
        $owner->setRefreshToken($refreshToken);

        $this->getEm()->persist($owner);
        $this->getEm()->flush();
    }
*/
}
