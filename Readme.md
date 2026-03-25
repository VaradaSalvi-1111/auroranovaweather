# 🌤️ AuroraNova — Weather Forecasting App

<p align="center">
  <img src="https://img.shields.io/badge/Status-Active-brightgreen?style=for-the-badge" />
  <img src="https://img.shields.io/badge/License-MIT-blue?style=for-the-badge" />
  <img src="https://img.shields.io/badge/Made%20with-❤️-red?style=for-the-badge" />
</p>

<p align="center">
  <b>Real-time weather forecasting with hourly & 5-day predictions, AI chatbot, user profiles, and more.</b>
</p>

---

## 📸 Screenshots

> _Add your screenshots here_

| Home | Weather Forecast | AI Chat |
|------|-----------------|---------|
| ![home](https://via.placeholder.com/280x160?text=Home) | ![forecast](https://via.placeholder.com/280x160?text=Forecast) | ![chat](https://via.placeholder.com/280x160?text=AI+Chat) |

---

## ✨ Features

- 🌡️ **Real-Time Weather** — Live weather data powered by OpenWeatherMap API
- ⏰ **Hourly Forecast** — Hour-by-hour breakdown for the current day
- 📅 **5-Day Forecast** — Plan ahead with a detailed 5-day outlook
- 📊 **Visual Graphs** — Line & temperature graphs for easy reading
- 🤖 **AI Chatbot** — Ask weather questions via built-in AI assistant
- 🎙️ **Voice Support** — Voice-enabled interaction
- 👤 **User Profiles** — Register, login, manage your account
- ❤️ **Favourites** — Save your favourite locations
- 💬 **Messaging** — Send & view messages between users
- 🔐 **Admin Panel** — Secure admin login & management
- 📱 **Responsive UI** — Smooth experience across all devices

---

## 🛠️ Tech Stack

<table>
  <tr>
    <td align="center" width="120">
      <a href="https://www.php.net/docs.php" target="_blank">
        <img src="https://skillicons.dev/icons?i=php" width="48" /><br/>
        <b>PHP</b>
      </a>
    </td>
    <td align="center" width="120">
      <a href="https://developer.mozilla.org/en-US/docs/Web/HTML" target="_blank">
        <img src="https://skillicons.dev/icons?i=html" width="48" /><br/>
        <b>HTML5</b>
      </a>
    </td>
    <td align="center" width="120">
      <a href="https://developer.mozilla.org/en-US/docs/Web/CSS" target="_blank">
        <img src="https://skillicons.dev/icons?i=css" width="48" /><br/>
        <b>CSS3</b>
      </a>
    </td>
    <td align="center" width="120">
      <a href="https://developer.mozilla.org/en-US/docs/Web/JavaScript" target="_blank">
        <img src="https://skillicons.dev/icons?i=js" width="48" /><br/>
        <b>JavaScript</b>
      </a>
    </td>
    <td align="center" width="120">
      <a href="https://www.mysql.com/documentation/" target="_blank">
        <img src="https://skillicons.dev/icons?i=mysql" width="48" /><br/>
        <b>MySQL</b>
      </a>
    </td>
    <td align="center" width="120">
      <a href="https://openweathermap.org/api" target="_blank">
        <img src="https://skillicons.dev/icons?i=azure" width="48" /><br/>
        <b>Weather API</b>
      </a>
    </td>
  </tr>
</table>

---

## 📁 Project Structure

```
AuroraNova/
├── 📄 index.php              # Entry point
├── 🏠 home.php               # Home page
├── 🌦️ send_weather.php       # Weather API handler
├── 📈 line_graph.php         # Hourly line graph
├── 🌡️ temp_graph.php         # Temperature graph
├── 🤖 ai.html / chatbot.js   # AI chatbot interface
├── 🎙️ voice.html / voice.js  # Voice interaction
├── 👤 profile.php            # User profile
├── 💬 chat.html / reply.php  # Messaging system
├── ❤️ favorites.php          # Saved locations
├── 🔐 admin_login.php        # Admin panel
├── 🗄️ db.php                 # Database connection
├── 🎨 style.css              # Global styles
└── 📜 script.js              # Main scripts
```

---

## ⚙️ Installation

### Prerequisites
- PHP `>= 7.4`
- MySQL / phpMyAdmin
- A local server like [XAMPP](https://www.apachefriends.org/) or [WAMP](https://www.wampserver.com/)
- [OpenWeatherMap API Key](https://openweathermap.org/api)

### Steps

```bash
# 1. Clone the repository
git clone https://github.com/yourusername/AuroraNova.git

# 2. Move to your server's root folder
#    e.g., htdocs/ for XAMPP

# 3. Import the database
#    Open phpMyAdmin → Create a new DB → Import the .sql file

# 4. Configure your database
#    Edit db.php with your DB credentials

# 5. Add your API key
#    Update send_weather.php with your OpenWeatherMap API key

# 6. Start your local server and visit:
http://localhost/AuroraNova/
```

---

## 🔑 Environment / Config

Update `db.php` with your credentials:

```php
$host     = "localhost";
$db_name  = "Aurora";
$username = "postgres";
$password = "";
```

Update `send_weather.php` with your API key:

```php
$api_key = "";
```

---

## 🤝 Contributing

Contributions are welcome! 🎉

1. Fork the project
2. Create your branch — `git checkout -b feature/AmazingFeature`
3. Commit your changes — `git commit -m 'Add AmazingFeature'`
4. Push to the branch — `git push origin feature/AmazingFeature`
5. Open a Pull Request

---

## 📄 License

This project is licensed under the **MIT License** — feel free to use and modify it.

---

## 🙋‍♂️ Author

<p align="center">
  Made with ❤️ by <b>Varada Salvi</b><br/>
  <a href="https://github.com/VaradaSalvi-1111">GitHub</a> •
  <a href="https://linkedin.com/in/varada-salvi-760814365">LinkedIn</a>
</p>

---

<p align="center">
  <img src="https://img.shields.io/badge/AuroraNova-Weather%20Forecasting-blueviolet?style=for-the-badge&logo=cloud&logoColor=white" />
</p>