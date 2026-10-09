# SRP: realtime service only. HTTP :3000 and Socket.io :3001 share this image.
# DIP: REDIS_HOST and LARAVEL_API are required at run time.
FROM node:20-alpine

WORKDIR /app

COPY package.json ./
RUN npm install --omit=dev

COPY --chown=node:node . .

USER node

EXPOSE 3000 3001

CMD ["npm", "start"]
