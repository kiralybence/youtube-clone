# YouTube clone

This is a minimalistic clone of YouTube made with Laravel.

It includes features such as:

- Video uploading
    - Automatic thumbnail generation
    - Multiple quality options
    - Custom thumbnails (TODO)
    - Public/Unlisted/Private visibility (TODO)
    - Preview GIF
- Search
- View count
- Comments
    - Upvotes, downvotes
    - Replies
- Like/Dislike
- Subscribing

## Prerequisites

This project is heavily based on FFmpeg, so make sure you have a working [FFmpeg](https://ffmpeg.org/) installation (a portable one works too).

You'll also need [Composer](https://getcomposer.org/) and [npm](https://www.npmjs.com/) to install the packages.

And of course... [PHP](https://www.php.net/) (7.3+), with a database that's supported by Laravel 8.

## Installation

Since this is Laravel, just follow the regular Laravel installation guide.

- First, install the Composer packages
```bash
composer install
```

- Then install and build the npm packages
```bash
npm install
npm run dev
```

- Create your `.env` file (based on `.env.example`). Don't forget to include the path to `ffmpeg` and `ffprobe` binaries.

- Run the database migrations
```bash
php artisan migrate
```

- Start your web server (or run `php artisan serve`)
