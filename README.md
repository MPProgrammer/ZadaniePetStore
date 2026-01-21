# Petstore – Laravel

Aplikacja demonstracyjna napisana w **Laravel**, komunikująca się z publicznym REST API **Swagger Petstore**.  
Projekt prezentuje obsługę CRUD dla zasobu **Pet**, prosty interfejs użytkownika oraz świadome podejście do integracji z zewnętrznym API.

Projekt jest dostępny na [petstore.codelarix.dev](https://petstore.codelarix.dev/).

---

## 🎯 Zakres funkcjonalny

Aplikacja umożliwia:

- wyświetlanie listy petów (filtrowanie po statusie)
- dodawanie nowego peta
- edycję istniejącego peta
- usuwanie peta
- obsługę błędów i komunikaty dla użytkownika

### Obsługiwane pola:
- `id` *(tylko do odczytu, widoczne na liście)*
- `name`
- `status`

Identyfikator `id` **nie jest edytowalny** - jest wyświetlany wyłącznie na liście petów.

---

## 🧱 Architektura

### Backend
- **Laravel (MVC)**
- Warstwa serwisowa (`PetstoreService`) jako adapter do zewnętrznego API
- Kontroler obsługuje:
  - renderowanie widoków (SSR)
  - endpointy AJAX (JSON)

### Frontend
- Blade templates
- Vanilla JavaScript (Fetch API)
- AJAX do:
  - pobierania listy petów
  - usuwania rekordów bez przeładowania strony
- Klasyczny SSR flow dla:
  - dodawania
  - edycji
  - redirectów i komunikatów

---

## 🔌 Integracja z API Petstore

Źródło API:
```
https://petstore.swagger.io/v2
```

Autoryzacja:
```
Header: api_key: special-key
```

### Obsługiwane endpointy:
- `GET /pet/findByStatus`
- `GET /pet/{petId}`
- `POST /pet`
- `PUT /pet`
- `DELETE /pet/{petId}`

---

## ⚡ Cache

Lista petów jest cache’owana:
- **per status** (`available`, `pending`, `sold`)
- przy użyciu **Laravel Cache (file driver)**

Czas cache jest **konfigurowalny** w pliku:

```php
config/pets.php
```

```php
'cache_duration_minutes' => 1,
```

Pozwala to łatwo dostosować czas cache bez modyfikowania kodu aplikacji.

### Cele cache:
- ograniczenie liczby zapytań do zewnętrznego API
- poprawa wydajności
- odporność na chwilowe błędy API demo

Cache jest **czyszczony** po:
- dodaniu
- edycji
- usunięciu peta

---

## 🔐 Bezpieczeństwo

- Ochrona CSRF dla wszystkich żądań POST
- Token CSRF przekazywany w nagłówku przy requestach AJAX
- Walidacja danych wejściowych po stronie backendu
- API key przechowywany wyłącznie po stronie serwera (`.env`)

---

## ⚠️ Znane ograniczenia i decyzje projektowe (Edge-cases)

### 5️⃣ Ograniczenia API Petstore (tworzenie nowych rekordów)

Podczas testów zaobserwowano nieprawidłowe zachowanie demonstracyjnego API Swagger Petstore przy dodawaniu nowych petów:

- API pozwala utworzyć nowego peta
- nowemu rekordowi przypisywany jest identyfikator `9223372036854775807`
- nowo dodany pet **nie pojawia się na liście petów**
- po odświeżeniu strony edycji (`/pets/{id}/edit`) API zwraca domyślne dane peta (`doggie`)

Zachowanie to wynika z charakteru API demonstracyjnego i nie jest związane z implementacją aplikacji.

W obecnej wersji aplikacji:
- możliwa jest **edycja oraz usuwanie istniejących petów**
- dodawanie nowych petów jest obsłużone po stronie aplikacji, jednak dane nie są spójnie zwracane przez API

W środowisku produkcyjnym problem ten zostałby rozwiązany poprzez:
- własne API backendowe
- synchronizację danych w bazie danych
- lub walidację odpowiedzi API po operacji zapisu

---

### 1️⃣ Identyfikatory jako string

API Petstore używa `int64` dla pola `id`.

Ze względu na ograniczenia precyzji liczb w JavaScript (`Number.MAX_SAFE_INTEGER`), identyfikatory są:
- traktowane jako **string**
- przesyłane do frontendu jako string
- nigdy nie rzutowane na `int`

Zapobiega to utracie precyzji i błędom przy operacjach CRUD.

---

### 2️⃣ Brak paginacji w API

Endpoint `findByStatus` nie udostępnia paginacji ani limitów.

Zastosowane rozwiązania:
- dane pobierane asynchronicznie (AJAX)
- ograniczenie liczby wyświetlanych rekordów po stronie frontendu
- cache po stronie backendu

Frontend został zaprojektowany w sposób umożliwiający **łatwą rozbudowę tabeli**  
(np. o sortowanie, filtrowanie, paginację przy użyciu bibliotek takich jak **DataTables**).

---

### 3️⃣ Kategorie, tagi i upload obrazów

API udostępnia modele (`Category`, `Tag`, `uploadImage`), jednak:
- brak endpointów do ich pobierania
- upload obrazu nie zwraca użytecznych danych
- brak możliwości realnego wykorzystania w UI

Z tego powodu:
- kategorie, tagi i obrazy **zostały świadomie pominięte**
- aplikacja skupia się na podstawowym CRUD zasobu `Pet`

---

### 4️⃣ Charakter API

Swagger Petstore jest API demonstracyjnym:
- niespójne metody (POST zamiast PUT w update)
- brak pełnych relacji
- brak webhooków i paginacji

Projekt pokazuje **adaptację do zewnętrznego API**, a nie jego idealne wykorzystanie domenowe.

---

## 🧪 Jakość kodu

Projekt został sprawdzony przy użyciu narzędzi:

- **PHPStan** – statyczna analiza kodu
- **PHP CS Fixer** – automatyczne formatowanie kodu

Style CSS zawierają **prefixy** zapewniające lepszą kompatybilność przeglądarek.

---

## 📌 Podsumowanie

Projekt demonstruje:
- czystą architekturę MVC
- bezpieczną integrację z zewnętrznym API
- obsługę edge-case’ów
- świadome decyzje techniczne
- gotowość do dalszej rozbudowy (DB, cron, paginacja, zaawansowane tabele)
