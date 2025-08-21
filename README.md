# 🚀 VentasPlus BI - Sistema de Cálculo de Comisiones

## 📖 Descripción
VentasPlus BI es una solución de **Business Intelligence** que automatiza el cálculo de comisiones de la fuerza de ventas.  
Integra datos desde archivos CSV, parametriza reglas de negocio y genera reportes e indicadores clave a través de un **API REST** y un **dashboard interactivo**.

---

## ✨ Características
- 📥 **ETL**: Importación de ventas y devoluciones desde archivos CSV.
- 💰 **Cálculo de Comisiones** con reglas:
  - Comisión base: **5%** del total de ventas.
  - Bono: **+2%** si el vendedor supera **$50,000,000 COP** en ventas mensuales.
  - Penalización: **-1%** si las devoluciones superan el **5%**.
- 📊 **Reportes consolidados** por vendedor (ventas, devoluciones, comisión base, bono, penalización y comisión final).
- 🌐 **API REST**: Endpoints para consultar las comisiones por período.
- 📈 **Dashboard con Chart.js**:
  - Top 5 vendedores por comisión.
  - Total de comisiones por mes.
  - Porcentaje de vendedores con bono.
- 🐳 **Soporte Docker** para despliegue rápido.

---

## ⚙️ Requisitos Técnicos
- PHP 7.4+ o 8.x
- MySQL 8.x
- Composer
- Node.js (opcional para frontend avanzado)
- Docker (opcional)

---

## 🚀 Instalación Rápida

1. **Clonar repositorio**
   ```bash
   git clone https://github.com/tuusuario/ventasplus-bi.git
   cd ventasplus-bi
   ```

2. **Instalar dependencias**
   ```bash
   composer install
   ```

3. **Configurar entorno**
   ```bash
   cp .env.example .env
   # Editar credenciales de BD en .env
   ```

4. **Crear base de datos**
   ```bash
   mysql -u root -p < database/migrations/001_setup.sql
   ```

5. **Cargar datos iniciales desde CSV**
   (usa los archivos `ventas_ejemplo_junio_julio.csv` y `ventas_con_devoluciones.csv` en `/data`)
   ```bash
   php scripts/etl/load_data.php
   ```

6. **Iniciar servidor**
   ```bash
   php -S localhost:8000 -t public
   ```

---

## 🌐 API REST

### Obtener comisiones por mes
```http
GET /api/comisiones?mes=6&ano=2025
```

**Ejemplo de respuesta:**
```json
{
  "success": true,
  "data": [
    {
      "vendedor": "Juan Perez",
      "ventas": 60000000,
      "devoluciones": 2000000,
      "comision_base": 3000000,
      "bono": 1200000,
      "penalizacion": 0,
      "comision_final": 4200000
    }
  ]
}
```

---

## 📊 Dashboard
Abrir en el navegador:  
👉 [http://localhost:8000](http://localhost:8000)  

Incluye gráficos:
- **Top 5 vendedores** por comisión.
- **Distribución de comisiones** por vendedor.
- **% vendedores con bono**.

---

## 🛠️ Desarrollo
- **Backend**: PHP 7.4+ con MVC.
- **Base de Datos**: MySQL.
- **Frontend**: Bootstrap + Chart.js.
- **ETL**: PHP scripts para carga de CSV.
- **Versionamiento**: GitHub + CI/CD (GitHub Actions).
- **Testing**: PHPUnit.

---

## 📦 Docker (opcional)
```bash
docker-compose up -d
```
Acceso:  
- App → [http://localhost:8080](http://localhost:8080)  
- PhpMyAdmin → [http://localhost:8081](http://localhost:8081)

---

## 📄 Entregables
- Código fuente en GitHub.
- Script SQL inicial (`database/migrations/001_setup.sql`).
- Script ETL (`scripts/etl/load_data.php`).
- Dashboard (`src/views/dashboard.php`).
- Documentación (`README.md`).
- Videos explicativos de la solución y la arquitectura.

---

## 🤝 Autor
Proyecto desarrollado como **Prueba Técnica - Ingeniero de Implementación BI** para **VentasPlus S.A.**
