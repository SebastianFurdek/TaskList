# Aplikácia pre správu úloh a projektov

Semestrálna práca z predmetu **Vývoj aplikácií pre internet a intranet (VAII)**.  
Webová aplikácia určená na správu úloh, projektov a kategórií s podporou používateľských účtov, autentifikácie a autorizácie.

---

## Autor

- **Meno:** Sebastián Furdek
- **Študijná skupina:** 5ZYI34
- **Akademický rok:** 2025/2026

---

## Popis aplikácie

Aplikácia umožňuje registrovaným používateľom:
- vytvárať a spravovať **projekty**
- vytvárať, upravovať a mazať **úlohy**
- priraďovať úlohy ku **projektom a kategóriám**
- označovať úlohy ako **dokončené** (AJAX)
- zobrazovať štatistiky (počet projektov, úloh, dokončené úlohy)
- pracovať s aplikáciou **len po prihlásení**

Súčasťou aplikácie je aj základná **rolová autorizácia** (používateľ / administrátor).

---

## Použité technológie

- **Backend:** PHP 8.x, Laravel 12
- **Frontend:** Blade, HTML5, CSS3, JavaScript
- **Databáza:** MySQL / MariaDB
- **Styling:** Bootstrap + vlastné CSS pravidlá
- **Verzionovanie:** Git
- **Build nástroje:** Node.js, Vite

---

## Funkcionalita aplikácie

- Registrácia, prihlásenie, odhlásenie používateľa
- CRUD operácie pre:
    - úlohy
    - projekty
    - kategórie
- Vzťahy medzi entitami (1:N, M:N)
- AJAX operácie (označenie úlohy ako dokončenej, dynamické aktualizácie)
- Validácia formulárov na strane klienta aj servera
- Responzívny dizajn

---

## Návod na inštaláciu a spustenie aplikácie

### 1. Klonovanie repozitára

```bash
git clone <URL_REPOZITÁRA>
cd TaskList
```

### 2. Inštalácia závislostí

```bash
composer install
npm install
npm run build
```
### 3. Konfigurácia prostredia
```bash
cp .env.example .env
```
Upravte súbor `.env` 
a nastavte pripojenie k databáze:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_manager
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generovanie aplikačného kľúča
```bash
php artisan key:generate
```
### 5. Migrácia databázy
```bash
php artisan migrate
```
### 6. Spustenie vývojového servera
```bash
php artisan serve
```
---
### sputenie vývojového prostredia
```bash
php artisan serve
npm run dev

```
---

## Použitie generatívnej AI

Pri vývoji aplikácie boli použité nástroje generatívnej umelej inteligencie:

- **ChatGPT** – vysvetlenie architektúry aplikácie, návrh riešení a pomoc pri ladení kódu
- **GitHub Copilot** – návrh CSS štýlov a pomocné návrhy JavaScriptového kódu




