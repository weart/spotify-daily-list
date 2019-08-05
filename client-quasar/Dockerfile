FROM  node:alpine

RUN apk --update add openssh-client git

RUN npm install -g vue-cli && \
    npm install -g @vue/cli@3.0.0 && \
    npm install -g @vue/cli-init@3.0.0 && \
    npm install -g quasar-cli@0.17.0 && \
    npm install -g http-server@0.11.1

# Prevent the reinstallation of node modules at every changes in the source code
#COPY package.json yarn.lock ./
#RUN yarn global add @api-platform/client-generator
#RUN npm install

RUN mkdir /home/node/app
#USER root

# VOLUME [ "/home/node/app" ]
WORKDIR /home/node/app

CMD /bin/sh

