# Discoveryfy (quasar-client)

Tiny WebApp for Share & Rate Songs.
Create collaborative playlists in Spotify with your friends and colleagues and rate the songs

#### Install the dependencies after docker-compose up (due to some problems in Dockerfile)
```bash
docker-compose exec client-quasar yarn global add @api-platform/client-generator
# ¿Why npm install instead of yarn?
docker-compose exec client-quasar npm install -g @quasar/cli
docker-compose exec client-quasar yarn install
docker-compose exec client-quasar yarn start
```


#### Start the app in development mode (hot-code reloading, error reporting, etc.)
```bash
quasar dev
```

#### Lint the files
```bash
yarn run lint
```

#### Build the app for production
```bash
quasar build
```

#### Executing 

#### Customize the configuration
See [Configuring quasar.conf.js](https://quasar.dev/quasar-cli/quasar-conf-js).

#### Roadmap
* Create poll view (/poll/#id), show tracks and videos
* Organizations
* Edit poll: Autofinish
* Poll image from first song



