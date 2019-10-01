# Discoveryfy (API)

Useful commands
-------
In order to execute symfony commands:
```shell
docker-compose exec php bin/console
```

Create poll:
```shell
curl -X POST "https://localhost:8443/polls" -H  "accept: application/ld+json" -H  "Content-Type: application/json" -d "{}"`
```

Create track:
```shell
curl -X POST "http://localhost:8080/tracks" -H  "accept: application/ld+json" -H  "Content-Type: application/json" -d "{\"spotify_uri\": \"spotify:track:1ECc1EhfkRx08o8uIwYOxW\",\"youtube_uri\": \"t67NhxJhrUU\",\"artist\": \"Lágrimas de Sangre\",\"name\": \"Rojos y separatistas\",\"poll\": \"6a3a946c-c0f5-4a2a-9a1c-ab230c051206\"}"
```
```
{
"spotify_uri": "spotify:track:1ECc1EhfkRx08o8uIwYOxW",
"youtube_uri": "t67NhxJhrUU",
"artist": "Lágrimas de Sangre",
"name": "Rojos y separatistas",
"poll": "6a3a946c-c0f5-4a2a-9a1c-ab230c051206"
}
```

Create vote:
```shell
curl -X POST "https://localhost:8443/votes" -H  "accept: application/ld+json" -H  "Content-Type: application/json" -d "{  \"name\": \"lenin\",  \"poll\": \"6a3a946c-c0f5-4a2a-9a1c-ab230c051206\",  \"track\": \"29b44e2b-7f55-4ef5-b462-43bcaa8f02f9\"}"`
```
```
{
"name": "lenin",
"poll": "6a3a946c-c0f5-4a2a-9a1c-ab230c051206",
"track": "29b44e2b-7f55-4ef5-b462-43bcaa8f02f9"
}
```

Executes queries in postgres:
```shell
docker-compose exec db psql --dbname api --username api-platform --password
```

Add fixtures:
```shell
docker-compose exec php bin/console doctrine:fixtures:load -n
```

Roadmap
-------
* Organizations
* Autofinish polls with a scheduled agent
* Bug with customized endpoint:
https://github.com/symfony/symfony/issues/32395
https://github.com/api-platform/api-platform/issues/588
