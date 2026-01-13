Iată traducerea și formatarea textului în limba română, păstrând stilul profesional de documentație tehnică pentru un fișier README.md:

AI Product Assistant – WooCommerce + Ollama (LLaMA 3)
Acest proiect este un plugin de WordPress care integrează un model de limbaj de mari dimensiuni (LLM) local, folosind Ollama (LLaMA 3), cu scopul de a automatiza generarea conținutului pentru produsele dintr-o platformă e-commerce WooCommerce.

Sistemul suportă două scenarii principale de utilizare:

Editare produse asistată de AI: generează descrieri optimizate SEO direct în editorul de produse WooCommerce.

Creare automată a produselor: creează produse complete (nume, descriere, preț, imagine, categorie) dintr-o singură interfață de administrare.

### 1. Caracteristici principale
Generare descrieri SEO: Creează descrieri de produs optimizate folosind un LLM local.

Creare automată de produse: Adaugă produse noi în WooCommerce folosind inteligența artificială.

Atribuire automată a categoriilor: Clasifică produsele în funcție de nume.

Gestionare imagini: Încărcare opțională a imaginilor și setarea automată a imaginii reprezentative (featured image).

Funcționare 100% locală: Nu necesită API-uri externe sau costuri suplimentare (confidențialitate totală).

Integrare nativă: Se integrează direct în interfața de administrare WordPress.

### 2. Cerințe de sistem
Software
XAMPP / WAMP / MAMP (Apache + MySQL + PHP).

WordPress (instalat local).

WooCommerce (plugin instalat și activat).

Ollama (instalat local).

Modelul LLaMA 3 descărcat în Ollama.

Git (opțional, pentru clonarea depozitului).

Hardware (recomandat)
Memorie RAM: Minimum 8 GB.

Procesor (CPU): Performanță multi-core bună.

Placă video (GPU): Opțional (Ollama suportă accelerare GPU pentru viteză sporită).

### 3. Instalarea Ollama și a modelului LLM
3.1 Instalare Ollama
Descarcă și instalează Ollama de la adresa: https://ollama.com/download

### 3.2 Verificare inițială Ollama

Înainte de a începe, asigură-te că **Ollama** rulează pe sistemul tău. Poți verifica accesând în browser:

> **URL:** `http://127.0.0.1:11434/api/tags`

---

### 4. Configurare WordPress + WooCommerce

* **Instalare locală:** Configurează WordPress (ex: în `xampp/htdocs/ecommerce-ai`).
* **Bază de date:** Creează o bază de date (ex: `ecommerce_ai`).
* **WooCommerce:** Instalează și activează plugin-ul **WooCommerce** din sectiunea **WordPress Admin**.

---

### 5. Instalarea Plugin-ului

1. Copiază folderul plugin-ului în calea:
`wp-content/plugins/ai-product-assistant/`
2. Activează plugin-ul din:
**Plugins** → **Installed Plugins** → **Activate**

---

### 6. Creare categorii de produse

Mergi la **Products** → **Categories** și creează următoarele categorii (folosește exact aceste **slug-uri**):

* **smartphones**
* **cameras**
* **headphones**
* **laptops**

---

### 7. Ghid de Utilizare

#### **Use Case 1 – Generare descriere pentru un produs existent**

1. Mergi la **Products** → **Add New** (sau editează un produs existent).
2. Introdu **Titlul Produsului**.
3. Apasă pe butonul **Generate AI Description**.
4. Descrierea va fi inserată automat în câmpul de conținut.

#### **Use Case 2 – Creare produs automat cu ajutorul AI**

1. Mergi la meniul **AI Product Assistant** din meniul lateral de admin.
2. Introdu **Numele produsului**, **Prețul** și o **Imagine** (opțional).
3. Click pe **Generate & Add Product**.
4. Produsul este creat automat în baza de date WooCommerce.

---

### 8. Detalii Tehnice (API)

Plugin-ul trimite cereri către **Ollama** local:

* **Endpoint:** `POST http://127.0.0.1:11434/api/generate`
* **Model utilizat:** `llama3`
* **Format cerere:**

```json
{
  "model": "llama3",
  "prompt": "Write an SEO product description...",
  "stream": false
}

```

---

### 9. Testare și Depanare

#### **Pași pentru testare**

* Verifică conexiunea: `http://127.0.0.1:11434/api/tags`.
* Testează funcția de adăugare rapidă din **AI Product Assistant Menu**.
* Testează generarea din editorul de produse (**Product Editor**).

#### **Troubleshooting (Remediere probleme)**

* Dacă **Ollama** nu răspunde, deschide un terminal și rulează:
* `ollama list` (verifică dacă serviciul e pornit).
* `ollama pull llama3` (asigură-te că modelul este descărcat).


* Dacă generarea este **lentă**, mărește timpul de timeout în configurarea PHP și evită titlurile de produse foarte lungi.

---

### 9. Structura Proiectului

```text
wp-content/plugins/ai-product-assistant/
└── ai-product-assistant.php


