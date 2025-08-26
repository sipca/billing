# 📞 SIPCA – Free Billing for Asterisk + FreePBX

SIPCA is a **free billing solution** for **Asterisk + FreePBX**, built with **PHP** using the **Yii2 framework**.  
It runs in **Docker** for easy deployment and integrates with FreePBX via **MySQL CDR** and **AMI**.

---

## 🚀 Requirements

Before you start, make sure you have the following installed:

- **FreePBX** (already configured and running)  
- **Docker**  
- **Docker Compose**  
- A **MySQL user** with read access to the FreePBX CDR database  
- An **AMI user** in FreePBX (edit `/etc/asterisk/manager.conf` and set `bindaddr = 0.0.0.0` instead of `127.0.0.1`)

---

## 🛠 Installation Guide

### 1. Clone the project
```bash
git clone https://github.com/sipca/billing
cd billing
````

### 2. Configure environment variables

Copy the example `.env` file:

```bash
cp .env.example .env
```

Update the values in `.env` with your MySQL, AMI, and integration settings.

---

## ⚙️ `.env` Configuration

| Variable                 | Description                                           |
| ------------------------ |-------------------------------------------------------|
| `MYSQL_HOST`             | Host of SIPCA’s internal MySQL database (docker container) |
| `MYSQL_DB`               | Database name for SIPCA                               |
| `MYSQL_USER`             | Username for SIPCA DB                                 |
| `MYSQL_PASSWORD`         | Password for SIPCA DB                                 |
| `AST_MYSQL_HOST`         | FreePBX MySQL host (where CDR is stored)              |
| `AST_MYSQL_DB`           | FreePBX CDR database name                             |
| `AST_MYSQL_USER`         | MySQL user with read access to FreePBX CDR            |
| `AST_MYSQL_PASSWORD`     | Password for the above user                           |
| `OUTBOUND_CONTEXT`       | Outbound dialplan context (default: `from-internal`)  |
| `TELEGRAM_BOT_API_KEY`   | Telegram Bot API key (optional, for notifications)    |
| `TELEGRAM_ADMIN_CHAT_ID` | Telegram chat ID of the admin (optional)              |
| `DIALER_TRUNK`           | Trunk name to be used by the dialer (optional)        |
| `DIALER_CONTEXT`         | Dialplan context for the dialer (optional)            |
| `AMI_HOST`               | FreePBX/Asterisk AMI host                             |
| `AMI_USERNAME`           | AMI username (created in FreePBX)                     |
| `AMI_SECRET`             | AMI password/secret                                   |

---

## ▶️ Run the project

### 3. Start the containers

```bash
docker compose up -d
```

### 4. Install PHP dependencies

```bash
docker compose exec backend composer install
```

### 5. Create an admin user

```bash
docker compose exec backend php yii user/create-admin admin admin
```

This will create a default admin user with:

* **Username:** `admin`
* **Password:** `admin`

---

## 🔑 Access the Web Interface

Once everything is running, open your browser and go to:

👉 [http://your-ip:21080](http://your-ip:21080)

Login with:

* **Username:** `admin`
* **Password:** `admin`

---

## ✅ Done!

You now have **SIPCA billing system** running with **Asterisk + FreePBX** 🎉
