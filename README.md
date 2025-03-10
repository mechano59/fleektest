# Laravel Docker Setup

This guide will help you set up and run a Laravel application using Docker.

## 🚀 Getting Started

### 1️⃣ Clone the Repository
```bash
git clone <your-repo-url>
cd <your-repo-name>
```

### 2️⃣ Build the Docker Image
```bash
docker build -t my-laravel-app .
```

### 3️⃣ Run the Container
```bash
docker run -p 8080:80 -p 33060:3306 my-laravel-app
```

### 4️⃣ Access the Application
Open your browser and visit:
```
http://localhost:8080/
```

---

## 🔧 Troubleshooting

### ❌ Error: `SQLSTATE[HY000] [1698] Access denied for user 'root'@'localhost'`

If you encounter this error:
```
Illuminate\Database\QueryException
SQLSTATE[HY000] [1698] Access denied for user 'root'@'localhost'
```

#### ✅ Solution:
1. Open your **Docker App**.
2. Go to **Actions** and select **Open in Terminal**.
3. Run the following commands:

```bash
mysql -u root -p
# Enter 'root' as the password when prompted

SELECT user, host FROM mysql.user;
SELECT * FROM mysql.db WHERE user = 'root';
```

If the output looks like this:

```
+-------------+-----------+
| User        | Host      |
+-------------+-----------+
| mariadb.sys | localhost |
| mysql       | localhost |
| root        | localhost |
+-------------+-----------+
```

Then run:
```bash
GRANT ALL PRIVILEGES ON fleektest01.* TO 'root'@'localhost' IDENTIFIED BY 'YOUR_ROOT_PASSWORD';
FLUSH PRIVILEGES;
exit;
```
Try again.

---

### ❌ Error: `SQLSTATE[HY000] [1049] Unknown database 'fleektest01'`

If you see this error:
```
Illuminate\Database\QueryException
SQLSTATE[HY000] [1049] Unknown database 'fleektest01'
```

#### ✅ Solution:
1. Open **Docker App**.
2. From **Actions**, select **Open in Terminal**.
3. Run:
```bash
chmod +x start.sh
./start.sh
```

Now your Laravel application should be running successfully! 🎉
