# SRP: dashboard dev server only. API and socket URLs are injected (DIP), not hardcoded.
FROM node:20-alpine

WORKDIR /app

COPY package.json ./
RUN npm install

COPY . .

EXPOSE 5173

CMD ["npm", "run", "dev"]
