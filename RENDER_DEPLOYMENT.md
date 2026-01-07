# NormNinja - Render Deployment Guide

This guide will help you deploy the NormNinja Laravel application on Render.

## Prerequisites

- A [Render account](https://render.com) (free tier available)
- Your code pushed to a Git repository (GitHub, GitLab, or Bitbucket)

## Quick Deployment Steps

### Option 1: Deploy with render.yaml (Recommended)

1. **Push your code to a Git repository** (if not already done)

2. **Sign in to Render**
   - Go to [https://render.com](https://render.com)
   - Sign in or create a new account

3. **Create a New Blueprint**
   - Click "New +" button in the dashboard
   - Select "Blueprint"
   - Connect your Git repository
   - Render will automatically detect the `render.yaml` file
   - Click "Apply" to create all services defined in the blueprint

4. **Configure Environment Variables**
   The render.yaml file configures most variables automatically, but you may need to set:
   - `APP_URL`: Your Render service URL (e.g., https://normninja.onrender.com)
   - `MAIL_HOST`, `MAIL_USERNAME`, `MAIL_PASSWORD`: If using email features

5. **Wait for Deployment**
   - Render will create the database and web service
   - The build process will run automatically
   - First deployment may take 5-10 minutes

### Option 2: Manual Deployment

#### Step 1: Create PostgreSQL Database

1. In Render dashboard, click "New +" → "PostgreSQL"
2. Configure:
   - **Name**: normninja-db
   - **Database**: normninja
   - **User**: normninja
   - **Region**: Choose closest to you
   - **Plan**: Free
3. Click "Create Database"
4. Save the connection details (Render will auto-populate them)

#### Step 2: Create Web Service

1. In Render dashboard, click "New +" → "Web Service"
2. Connect your Git repository
3. Configure:
   - **Name**: normninja
   - **Region**: Same as database
   - **Branch**: main (or your default branch)
   - **Runtime**: PHP
   - **Build Command**: `bash build.sh`
   - **Start Command**: `bash start.sh`
   - **Plan**: Free

#### Step 3: Configure Environment Variables

Add the following environment variables in the Render dashboard:

**Essential Variables:**
```
APP_NAME=NormNinja
APP_ENV=production
APP_DEBUG=false
APP_KEY=<generate a base64 key>
APP_URL=https://your-app.onrender.com

LOG_CHANNEL=stack
LOG_LEVEL=info
```

**Database Variables** (auto-filled from database):
```
DB_CONNECTION=pgsql
DB_HOST=<from database>
DB_PORT=<from database>
DB_DATABASE=<from database>
DB_USERNAME=<from database>
DB_PASSWORD=<from database>
```

**Session & Cache:**
```
SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
BROADCAST_DRIVER=log
SESSION_LIFETIME=120
```

**Mail Configuration** (Optional - only if using email):
```
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@normninja.com
MAIL_FROM_NAME=NormNinja
```

#### Step 4: Generate APP_KEY

To generate a secure APP_KEY:
```bash
php artisan key:generate --show
```
Copy the output and set it as the APP_KEY environment variable.

#### Step 5: Deploy

1. Click "Create Web Service"
2. Render will start building and deploying your application
3. Monitor the logs for any errors

## Post-Deployment Setup

### Create Admin User

Once deployed, you need to create an admin user. You can do this using Render Shell:

1. Go to your web service in Render dashboard
2. Click "Shell" tab
3. Run the following command:

```bash
php artisan tinker
```

Then in tinker:
```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@normninja.com',
    'password' => bcrypt('YourSecurePassword123!'),
    'role' => 'admin',
    'is_active' => true
]);
exit;
```

### Access Your Application

Your application will be available at:
```
https://normninja.onrender.com
```
(Replace with your actual Render URL)

## Important Notes

### Free Tier Limitations

- **Web Service**:
  - Spins down after 15 minutes of inactivity
  - First request after spin-down may take 30-60 seconds
  - 750 hours/month of uptime

- **Database**:
  - 1GB storage
  - Expires after 90 days (free tier)
  - Data is backed up

### Storage Considerations

- Render's free tier has ephemeral filesystem
- Uploaded files will be lost when the service restarts
- For production, consider using:
  - AWS S3 for file storage
  - Cloudinary for image storage
  - Or upgrade to Render paid plan with persistent disks

### Database: PostgreSQL vs MySQL

This deployment uses PostgreSQL (Render's free database). If your app was originally built for MySQL, Laravel handles the differences automatically. However, if you encounter issues:

1. Check database migrations for MySQL-specific syntax
2. Update any raw SQL queries to be PostgreSQL compatible
3. Test all database operations after deployment

## Troubleshooting

### Build Fails

**Issue**: Build command fails
**Solution**: Check the build logs in Render dashboard. Common issues:
- Missing PHP extensions: Add them to render.yaml
- Composer dependency conflicts: Update composer.json
- Node version issues: Specify Node version in render.yaml

### Database Connection Errors

**Issue**: Cannot connect to database
**Solution**:
- Verify database environment variables are set correctly
- Check that the database is in the same region as web service
- Ensure DB_CONNECTION is set to `pgsql`

### 500 Internal Server Error

**Issue**: Application shows 500 error
**Solution**:
1. Check logs: `Settings → Logs` in Render dashboard
2. Ensure APP_KEY is set
3. Verify all environment variables are configured
4. Run: `php artisan config:clear` in Shell

### Storage/Permission Issues

**Issue**: File upload or cache errors
**Solution**: Run in Render Shell:
```bash
chmod -R 775 storage bootstrap/cache
php artisan storage:link
php artisan config:cache
```

### Application Slow After Inactivity

**Issue**: First request is very slow
**Solution**: This is normal for free tier (cold start). Options:
- Upgrade to paid plan (no spin down)
- Use an uptime monitoring service to keep it active
- Accept the cold start delay

## Updating Your Application

### Deploy New Changes

1. Push changes to your Git repository
2. Render will automatically detect and deploy changes
3. Monitor deployment in Render dashboard

### Manual Deployment

If auto-deploy is disabled:
1. Go to your web service in Render
2. Click "Manual Deploy" → "Deploy latest commit"

### Run Migrations

After deploying changes that include new migrations:
1. Go to Shell tab in Render
2. Run: `php artisan migrate --force`

### Clear Caches

If configuration changes:
1. Go to Shell tab
2. Run:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## Monitoring

### View Logs

- Go to your web service in Render dashboard
- Click "Logs" tab
- View real-time application logs

### Check Metrics

- Monitor CPU, memory, and bandwidth usage
- Available in "Metrics" tab

## Security Checklist

- [ ] `APP_DEBUG` is set to `false`
- [ ] `APP_ENV` is set to `production`
- [ ] Strong `APP_KEY` is generated and set
- [ ] Database credentials are secure
- [ ] Default admin password changed
- [ ] HTTPS is enabled (automatic on Render)
- [ ] CORS is configured properly
- [ ] Rate limiting is enabled
- [ ] File upload limits are set

## Cost Optimization

### Free Tier
- Use free tier for development/testing
- Accept spin-down behavior
- Monitor 750 hours/month limit

### Paid Tier ($7-25/month)
- No spin-down (always active)
- Persistent disks for file storage
- Better performance
- Custom domains with SSL

## Support

### Render Support
- [Render Documentation](https://render.com/docs)
- [Community Forum](https://community.render.com)
- Support: support@render.com

### Application Support
- Check logs: `storage/logs/laravel.log`
- Review documentation
- Contact Data Voyagers Team

## Additional Resources

- [Render PHP Deployment Guide](https://render.com/docs/deploy-php)
- [Laravel Deployment Documentation](https://laravel.com/docs/deployment)
- [Render Environment Variables](https://render.com/docs/environment-variables)

---

**Deployed by Data Voyagers Team**
