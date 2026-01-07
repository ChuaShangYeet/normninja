# NormNinja - Aiven Database Setup Guide

This guide explains how to set up an Aiven PostgreSQL database for use with your NormNinja application deployed on Render.

## Why Use Aiven for Database Hosting?

**Advantages:**
- **Better Free Tier**: Aiven offers a more generous free tier than Render's database
- **No Expiration**: Free tier databases don't expire after 90 days (Render limitation)
- **Reliability**: Professional-grade managed PostgreSQL service
- **Global Availability**: Multiple cloud providers and regions
- **Advanced Features**: Automatic backups, monitoring, and performance insights
- **Easy Scaling**: Upgrade plans as your app grows

## Prerequisites

- An [Aiven account](https://aiven.io) (free tier available)
- Your Render web service deployed (see RENDER_DEPLOYMENT.md)

## Step-by-Step Setup

### 1. Create Aiven Account

1. Go to [https://aiven.io](https://aiven.io)
2. Click "Sign Up" and create a free account
3. Verify your email address

### 2. Create PostgreSQL Database

1. **Log in to Aiven Console**
   - Go to [https://console.aiven.io](https://console.aiven.io)

2. **Create New Service**
   - Click "Create Service" or "Create a new service"
   - Select **PostgreSQL**

3. **Select Cloud Provider**
   - Choose your preferred cloud provider:
     - **AWS** (Amazon Web Services)
     - **Google Cloud**
     - **Azure**
   - Recommendation: Choose the same region as your Render service (Oregon/us-west if possible)

4. **Select Service Plan**
   - Choose **Free** plan (Hobbyist)
   - Plan includes:
     - 1 node
     - 1 GB RAM
     - 5 GB storage
     - Shared CPU

5. **Configure Service**
   - **Service Name**: `normninja-db` (or your preferred name)
   - **Cloud Region**: Select closest to your Render deployment
   - **PostgreSQL Version**: Use default (latest stable)

6. **Create Service**
   - Review settings
   - Click "Create service"
   - Wait 3-5 minutes for service to initialize

### 3. Get Connection Details

Once your PostgreSQL service is running:

1. **Go to Service Overview**
   - Click on your `normninja-db` service

2. **Find Connection Information**
   - Scroll to "Connection information" section
   - You'll see:
     - **Host**: `your-service.aivencloud.com`
     - **Port**: Usually `12345` (custom port)
     - **User**: `avnadmin`
     - **Password**: Click "Show" to reveal
     - **Database**: `defaultdb`
     - **SSL Mode**: `require`

3. **Copy Credentials**
   - Keep this tab open or copy credentials to a secure location
   - You'll need these for Render configuration

### 4. Configure Database Access

Aiven databases require SSL connections by default (which is good for security).

**Optional: Download SSL Certificate**
- In the Aiven Console, under "Overview"
- Find "CA Certificate" and download if needed (Laravel handles this automatically with `sslmode=require`)

### 5. Configure Render Environment Variables

Now connect your Render web service to Aiven database:

1. **Go to Render Dashboard**
   - Navigate to [https://dashboard.render.com](https://dashboard.render.com)
   - Select your `normninja` web service

2. **Open Environment Variables**
   - Click "Environment" in the left sidebar

3. **Add/Update Database Variables**

   Set these environment variables with your Aiven credentials:

   ```
   DB_CONNECTION=pgsql
   DB_HOST=<your-service>.aivencloud.com
   DB_PORT=<your-aiven-port>
   DB_DATABASE=defaultdb
   DB_USERNAME=avnadmin
   DB_PASSWORD=<your-aiven-password>
   DB_SSLMODE=require
   ```

   **Example:**
   ```
   DB_CONNECTION=pgsql
   DB_HOST=normninja-db-myproject.aivencloud.com
   DB_PORT=12345
   DB_DATABASE=defaultdb
   DB_USERNAME=avnadmin
   DB_PASSWORD=AVNS_abc123xyz789...
   DB_SSLMODE=require
   ```

4. **Save Changes**
   - Click "Save Changes"
   - Render will automatically redeploy your application

### 6. Verify Connection

1. **Check Deployment Logs**
   - In Render dashboard, go to "Logs"
   - Look for successful database migration messages:
     ```
     🗄️  Running database migrations...
     Migration table created successfully.
     Migrating: [migration names]...
     ```

2. **Test in Render Shell**
   - Go to "Shell" tab in Render dashboard
   - Run:
     ```bash
     php artisan migrate:status
     ```
   - Should show all migrations ran successfully

### 7. Initialize Database

Create your admin user and seed data:

1. **Open Render Shell**
   - In your web service, click "Shell" tab

2. **Create Admin User**
   ```bash
   php artisan tinker
   ```

   Then in Tinker:
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

3. **Verify User Created**
   ```bash
   php artisan tinker
   ```
   ```php
   \App\Models\User::count();
   // Should return: 1
   exit;
   ```

## Aiven Database Management

### Accessing Your Database

**Via Aiven Console (Web Interface):**
1. Go to your service in Aiven Console
2. Click on "Database" tab
3. Use built-in query editor

**Via psql (Command Line):**
```bash
psql "postgres://avnadmin:<password>@<host>:<port>/defaultdb?sslmode=require"
```

**Via Database Client (TablePlus, DBeaver, pgAdmin):**
- Host: `<your-service>.aivencloud.com`
- Port: `<your-port>`
- Database: `defaultdb`
- Username: `avnadmin`
- Password: `<your-password>`
- SSL: Required

### Monitoring

**In Aiven Console:**
1. Go to your service
2. Click "Metrics" tab
3. View:
   - CPU usage
   - Memory usage
   - Disk usage
   - Connection count
   - Query performance

### Backups

**Automatic Backups:**
- Aiven automatically backs up your database
- Free tier: 2-day retention
- View backups in "Backups" tab

**Manual Backup:**
```bash
# Export database
pg_dump "postgres://avnadmin:<password>@<host>:<port>/defaultdb?sslmode=require" > backup.sql

# Restore database
psql "postgres://avnadmin:<password>@<host>:<port>/defaultdb?sslmode=require" < backup.sql
```

### Creating Additional Databases

If you want separate databases for different environments:

1. Connect via psql or database client
2. Run:
   ```sql
   CREATE DATABASE normninja_production;
   CREATE DATABASE normninja_staging;
   ```

3. Update `DB_DATABASE` in Render environment variables

## Troubleshooting

### Connection Refused

**Issue**: Cannot connect to Aiven database

**Solutions:**
1. Verify service is running in Aiven Console (status: "Running")
2. Check credentials are correct
3. Ensure `DB_SSLMODE=require` is set
4. Verify no typos in host/port

### SSL Connection Errors

**Issue**: SSL-related connection errors

**Solution:**
```bash
# In Render Shell, verify SSL
php artisan tinker
```
```php
DB::connection()->getPdo();
// Should connect without errors
```

If issues persist, ensure:
- `DB_SSLMODE=require` is set
- Using correct host from Aiven (ends with `.aivencloud.com`)

### Migration Failures

**Issue**: Migrations fail to run

**Solutions:**
1. Check database credentials
2. Verify database exists:
   ```bash
   php artisan db:show
   ```
3. Check migration files for syntax errors
4. Run migrations manually:
   ```bash
   php artisan migrate --force
   ```

### Slow Queries

**Issue**: Database queries are slow

**Solutions:**
1. Check Aiven metrics for resource usage
2. Add database indexes:
   ```bash
   php artisan make:migration add_indexes_to_tables
   ```
3. Enable query logging:
   - In Aiven Console → "Logs" tab
   - Enable "pg_stat_statements"

### Out of Connections

**Issue**: Too many database connections

**Solution:**
- Free tier has limited connections (~25)
- Check connection pooling settings
- Ensure Laravel closes connections properly
- Consider upgrading Aiven plan

## Upgrading Your Plan

When you outgrow the free tier:

1. **Go to Aiven Console**
2. **Select your service**
3. **Click "Overview" → "Change plan"**
4. **Choose a paid plan:**
   - Startup: $35/month (4 GB RAM, 80 GB storage)
   - Business: $100/month (8 GB RAM, 175 GB storage)
   - Premium: Custom pricing

5. **Apply changes** (zero downtime upgrade)

## Cost Comparison

| Provider | Free Tier | Limitations |
|----------|-----------|-------------|
| **Aiven** | 1 GB RAM, 5 GB storage | None (permanent free tier) |
| **Render** | 1 GB storage | Expires after 90 days |
| **Heroku** | No free tier | Paid only ($5+/month) |

## Security Best Practices

- ✅ **Always use SSL** (`DB_SSLMODE=require`)
- ✅ **Rotate passwords regularly** (in Aiven Console → "Users")
- ✅ **Use environment variables** (never commit credentials)
- ✅ **Enable backups** (automatic on Aiven)
- ✅ **Monitor access logs** (Aiven Console → "Logs")
- ✅ **Restrict database access** (Aiven has IP allowlisting)
- ✅ **Use strong passwords** (Aiven generates these automatically)

## Next Steps

After setting up Aiven:

1. ✅ Database created and running
2. ✅ Connected to Render application
3. ✅ Migrations ran successfully
4. ✅ Admin user created
5. 🎉 Your application is ready to use!

## Support

**Aiven Support:**
- [Aiven Documentation](https://docs.aiven.io)
- [Support Portal](https://aiven.io/support)
- [Community Forum](https://aiven.io/community)

**Application Support:**
- Check logs: Render Dashboard → Logs
- Review Laravel logs: Render Shell → `tail -f storage/logs/laravel.log`
- Contact Data Voyagers Team

---

**Developed by Data Voyagers Team**
