# YouTube clone

This is a minimalistic clone of YouTube created with Laravel.

## Main features

- Video uploading
    - Automatic thumbnail generation
    - Custom thumbnails (TODO)
    - Public/Unlisted/Private visibility (TODO)
- Video preview on hover
- Multiple playback quality options
- Search
- View count (with spam detection)
- Comments
    - Upvote/Downvote
    - Replies
- Like/Dislike
- Subscribing

## Setup

### Prerequisites

- PHP 7.3 (or higher)
- MySQL 5.7 (or higher)
- [Composer](https://getcomposer.org/)
- [npm](https://www.npmjs.com/)
- [FFmpeg](https://ffmpeg.org/)

### Installation (for development)

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

- Create (and fill out) your `.env` file
```bash
cp .env.example .env
```

- Run the database migrations
```bash
php artisan migrate
```

- Serve your application
```bash
php artisan serve
```
