# 🔧 DentalMX - MySQL Connection Troubleshooting Guide

## Quick Fix Checklist ⚡

- [ ] MySQL server is running (check XAMPP/MAMP control panel)
- [ ] Port in `.env` matches your MySQL server port
- [ ] Database `engsigne_magic_dental` exists
- [ ] `.env` file is properly configured
- [ ] Browser cache is cleared

---

## Error Symptoms

### ❌ Error: "Unable to connect to the database"
**Full message:**
```
ERROR - ... Error DB: Unable to connect to the database.
Main connection [MySQLi]: No se puede establecer una conexión...
```

**Possible causes:**
1. MySQL server is not running
2. Port mismatch between `.env` and actual MySQL port
3. Database doesn't exist
4. Wrong credentials in `.env`
5. MySQL socket connection issues

---

## Platform-Specific Diagnosis

### 🖥️ Windows with XAMPP

**Automatic Diagnosis:**
```batch
cd path\to\DentalMX_Joss
diagnose-mysql-windows.bat
```

**Manual Steps:**

1. **Verify XAMPP is running:**
   - Open `C:\xampp\xampp-control.exe`
   - Check that "MySQL" shows a green checkmark
   - If red, click "Start" next to MySQL

2. **Check MySQL Port:**
   - Open Command Prompt
   - Run: `netstat -ano | find ":3306"`
   - If you see output, MySQL is listening on 3306

3. **Test Connection:**
   - Open Command Prompt
   - Run: `cd C:\xampp\mysql\bin`
   - Run: `mysql -u root -h 127.0.0.1 -P 3306`
   - If you get `mysql>` prompt, connection works

4. **Update `.env` if needed:**
   ```env
   database.default.hostname = localhost
   database.default.port = 3306
   database.default.username = root
   database.default.password = 
   database.default.database = engsigne_magic_dental
   ```

---

### 🍎 macOS with MAMP

**Automatic Diagnosis:**
```bash
cd path/to/DentalMX_Joss
chmod +x diagnose-mysql-macos.sh
./diagnose-mysql-macos.sh
```

**Manual Steps:**

1. **Verify MAMP is running:**
   - Open MAMP application
   - Click "Start Servers"
   - Wait for Apache and MySQL indicators to turn green

2. **Check MySQL Port:**
   - MAMP typically uses port **3306**
   - To verify, run in Terminal:
     ```bash
     lsof -i | grep mysql
     ```
   - Look for the port number (usually 3306)

3. **Test Connection:**
   - Run in Terminal:
     ```bash
     /Applications/MAMP/Library/bin/mysql -u root -h 127.0.0.1
     ```
   - If you get `mysql>` prompt, connection works

4. **Update `.env`:**
   ```env
   database.default.hostname = localhost
   database.default.port = 3306
   database.default.username = root
   database.default.password = 
   database.default.database = engsigne_magic_dental
   ```

---

### 🐧 Linux / Docker

**Verify MySQL is running:**
```bash
# If using Docker
docker ps | grep mysql

# If MySQL is installed locally
sudo service mysql status

# Or
mysqld --version
```

**Check connection:**
```bash
mysql -u root -h localhost -p
# If no password, just press Enter when prompted
```

**Update `.env`:**
```env
database.default.hostname = localhost
database.default.port = 3306
database.default.username = root
database.default.password = 
database.default.database = engsigne_magic_dental
```

---

## Configuration Reference

### Default Credentials

| Parameter | Default Value | Notes |
|-----------|---------------|-------|
| Host | `localhost` | Use `127.0.0.1` if localhost fails |
| Port | `3306` | Check your server config |
| User | `root` | Standard default |
| Password | (empty) | XAMPP/MAMP come with no password |
| Database | `engsigne_magic_dental` | Must be created first |

### Port Reference by Platform

| Platform | Port | Notes |
|----------|------|-------|
| XAMPP (Windows) | `3306` | Standard MySQL port |
| MAMP (macOS) | `3306` | Check MAMP preferences |
| Docker | `3306` | Usually mapped |
| Homebrew (macOS) | `3306` | If using mysql@5.7 or similar |
| Amazon RDS | `3306` | Or custom port |
| DigitalOcean | `3306` | Or custom port |
| Azure MySQL | `3306` | Firewall may block |
| Google Cloud SQL | `3306` | Requires proxy or public IP |

---

## Database Verification

### Check if database exists

**Using XAMPP/MAMP GUI (phpMyAdmin):**
1. Open your browser to `http://localhost:8080/phpmyadmin/` (or appropriate port)
2. Look for `engsigne_magic_dental` in the database list on the left
3. If not there, import from `database/schema.sql`

**Using Command Line:**

**Windows:**
```batch
cd C:\xampp\mysql\bin
mysql -u root -e "SHOW DATABASES;"
```

**macOS/Linux:**
```bash
/Applications/MAMP/Library/bin/mysql -u root -e "SHOW DATABASES;"
# Or if mysql is in PATH:
mysql -u root -e "SHOW DATABASES;"
```

### Verify tables exist

**Windows:**
```batch
cd C:\xampp\mysql\bin
mysql -u root engsigne_magic_dental -e "SHOW TABLES;"
```

**macOS/Linux:**
```bash
/Applications/MAMP/Library/bin/mysql -u root engsigne_magic_dental -e "SHOW TABLES;"
# Or if mysql is in PATH:
mysql -u root engsigne_magic_dental -e "SHOW TABLES;"
```

---

## Advanced Debugging

### View Application Error Logs

**Recent errors:**
```bash
tail -50 writable/logs/log-*.log
```

**Search for database errors:**
```bash
grep -i "database\|connection\|mysqli" writable/logs/log-*.log
```

**Full log of today:**
```bash
cat writable/logs/log-$(date +%Y-%m-%d).log
```

### Clear Cache and Logs

**Caution: This removes cached data**

**Windows:**
```batch
rmdir /S /Q writable\cache
del /Q writable\logs\log-*.log
```

**macOS/Linux:**
```bash
rm -rf writable/cache/*
rm -f writable/logs/log-*.log
```

### Enable Debug Mode

Edit `.env`:
```env
# Change from:
CI_ENVIRONMENT = production

# To:
CI_ENVIRONMENT = development
```

This will show more detailed error messages (don't use in production).

---

## Common Issues & Solutions

### Issue 1: "Port already in use"

**On Windows:**
```batch
# Find what's using port 3306
netstat -ano | find ":3306"

# In the output, note the PID (Process ID) from the last column
# Then kill it (replace 12345 with actual PID)
taskkill /PID 12345 /F

# Or restart MySQL in XAMPP
```

**On macOS:**
```bash
# Find process using port 3306
lsof -i :3306

# Kill it (replace PID with actual PID)
kill -9 PID
```

### Issue 2: "Access denied for user 'root'"

Check if password is needed:

**Windows:**
```batch
# Try connecting without password
C:\xampp\mysql\bin\mysql -u root

# If that fails, try with password
C:\xampp\mysql\bin\mysql -u root -p
# Then type your password
```

**macOS:**
```bash
# Try connecting without password
/Applications/MAMP/Library/bin/mysql -u root

# If that fails, try with password
/Applications/MAMP/Library/bin/mysql -u root -p
# Then type your password
```

Update `.env` with the correct password:
```env
database.default.password = your_password_here
```

### Issue 3: "Can't connect to MySQL server on 'localhost'"

This usually means the socket connection is failing. Try using IP instead:

```env
# Change from:
database.default.hostname = localhost

# To:
database.default.hostname = 127.0.0.1
```

### Issue 4: Database exists but tables are empty

Import the schema:

**Windows GUI:**
1. Open phpMyAdmin: `http://localhost:8080/phpmyadmin/`
2. Click on `engsigne_magic_dental`
3. Go to "Import" tab
4. Click "Choose File"
5. Select `database/schema.sql`
6. Click "Import"

**Windows Command Line:**
```batch
cd C:\xampp\mysql\bin
mysql -u root engsigne_magic_dental < C:\path\to\database\schema.sql
```

**macOS/Linux:**
```bash
/Applications/MAMP/Library/bin/mysql -u root engsigne_magic_dental < /path/to/database/schema.sql
# Or:
mysql -u root engsigne_magic_dental < /path/to/database/schema.sql
```

---

## Performance Tips

### Optimize MySQL Connection

If you experience slowness, try:

1. **Enable query caching** (in MySQL config)
2. **Add proper indexes** (should already be in schema)
3. **Use connection pooling** (if available in your setup)
4. **Monitor slow queries**:
   ```bash
   # View slow queries
   mysql -u root -e "SHOW PROCESSLIST;"
   ```

### Monitor Database

**Check database size:**
```bash
# Windows
C:\xampp\mysql\bin\mysql -u root -e "SELECT table_schema, SUM(data_length + index_length) / 1024 / 1024 AS MB FROM information_schema.tables GROUP BY table_schema;"

# macOS/Linux
mysql -u root -e "SELECT table_schema, SUM(data_length + index_length) / 1024 / 1024 AS MB FROM information_schema.tables GROUP BY table_schema;"
```

---

## Getting Help

If you've tried everything above and still have issues:

1. **Note down your error:**
   - Full error message (copy from page)
   - What you were trying to do

2. **Collect diagnostic data:**
   - Run the appropriate diagnostic script
   - Save the output
   - Check the error logs (`writable/logs/log-*.log`)

3. **Provide to support:**
   - OS and platform (Windows/macOS/Linux)
   - XAMPP/MAMP version
   - MySQL port
   - Your `.env` file (with password hidden)
   - Error messages
   - Diagnostic script output

---

## Additional Resources

- [XAMPP MySQL Documentation](https://www.apachefriends.org/)
- [MAMP Documentation](https://documentation.mamp.info/)
- [CodeIgniter Database Guide](https://codeigniter.com/user_guide/database/)
- [MySQL Documentation](https://dev.mysql.com/doc/)

---

**Last Updated:** December 9, 2025  
**Version:** 1.0.0  
**Maintained by:** Roesch Studio
