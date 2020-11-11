# YouTube Feed Exporter

## How to setup (locally)

1. Clone this repository
2. Install dependencies via `composer install`
3. Run `php vendor/bin/homestead make`
4. Run `vagrant up`
5. When the box is ready log in to the box via `vagrant ssh`
6. Inside the box navigate to `/home/vagrant/code`
7. Run `cp .env.example .env`
8. Run `php artisan key:generate`
9. Run `php artisan migrate`
10. Go to https://console.developers.google.com/ and create a project
11. Inside the project activate the "YouTube Data API v3"
12. Create an OAuth-Approval-Screen
    - Add the `.../auth/youtube.readonly` section
    - Add your email address as a trusted user
13. Create an API key
    - Save the token to the `.env` file as `YOUTUBE_API_KEY` (at the bottom of the file)
14. Create an OAuth 2.0 client
    - Type: web application
    - Name: "YouTube Feed Exporter" (e.g.)
    - Authorised redirect uris: `http://localhost:8000/oauth/youtube/handle`
    - Save the client id to the `.env` file as `YOUTUBE_CLIENT_ID` (at the bottom of the file)
    - Save the client secret to the `.env` file as `YOUTUBE_CLIENT_SECRET` (at the bottom of the file)
    
## Usage

Navigate to http://localhost:8000/. You'll get redirected to YouTube/Google to authenticate
and authorize the app/client you just created.
When you're done, you'll get redirected back to http://localhost:8000/ and get your subscriptions as JSON.


