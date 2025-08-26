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

Follow these steps to get SIPCA up and running:

### 1. Clone the project
```bash
git clone https://github.com/sipca/sip-billing
cd sip-billing
````

### 2. Configure environment variables

Copy the example `.env` file and edit it:

```bash
cp .env.example .env
```

Update the file with your **MySQL** credentials and **AMI user** details.

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

---

