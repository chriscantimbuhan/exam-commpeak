# CommPeak PHP Pratical Exam

## Requirements:
- MySql v8
- PHP v7.4^
- NodeJs v20.*
- Composer v2.*

## Installation

## BACKEND
Navigate to the project folder via terminal.
```sh
cd project_folder
```

Create your MySql database.

Install composer packages.
```sh
composer install
```
Copy or move env.sample to .env.
```sh
cp env.sample .env
```

Configure .env depending on your machine.
Variables to check:
- DATABASE_URL
- IP_GEO_API_KEY
- IP_GEO_HOST_URL
- PHONE_INFO_URL

Clear cache (as needed)
```sh
php bin/console cache:clear
```

Run database migrations:
```sh
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

## FRONTEND
Navigate to frontend folder from project directory.
```sh
cd frontend
```

Install node packages.
```sh
npm install
```

## TO TEST
Need 3 terminals for the following:
- Backend:
```sh
symfony server:start
```

- Messenger (Queue Processing):
```sh
bin/console messenger:consume async
```

- Frontend:
```sh
npm run start
```

Now access the link via browser to check:
```sh
http://127.0.0.1:3000/
```
OR
```sh
http://localhost:3000/
```