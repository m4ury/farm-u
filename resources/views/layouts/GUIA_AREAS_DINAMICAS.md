# Estructura Dinámica de Áreas de Medicamentos

## Descripción

El sistema ahora cuenta con una estructura dinámica y reutilizable para mostrar medicamentos por área. Está compuesto por:

### Componentes

1. **`resources/views/components/medicamentos-table.blade.php`**
   - Componente reutilizable que renderiza la tabla de medicamentos
   - Recibe: `$items` (medicamentos) y `$showActions` (bool)
   - Incluye lógica de estado de vencimiento, bajo stock, controlado, etc.

2. **`resources/views/layouts/area-medicamentos.blade.php`**
   - Layout base dinámico que todas las vistas de áreas heredan
   - Recibe: `$titulo` (string) y `$areas` (collection)
   - Incluye DataTables, SweetAlert y scripts necesarios

### Vistas Existentes

- Ahora toda las áreas usan una **vista genérica única**: `resources/views/areas/show.blade.php`
- Cada área solo cambia de **título** (obtenido del array de configuración en el controlador)

---

## Cómo Agregar una Nueva Área

### Paso 1: Agregar Configuración en AreaController
En `app/Http/Controllers/AreaController.php`, en el método `getAreaConfig()`:

```php
private function getAreaConfig(){
    return [
        'botiquin' => ['botiquín urgencias', 'Botiquin Urgencias'],
        'carro' => ['carro de paro urgencias', 'Carro de paro Urgencias'],
        'maletin' => ['maletín urgencias', 'Maletin Urgencias'],
        'nueva-area' => ['nombre de área en BD', 'Nueva Área'],  // ← Agregar aquí
    ];
}
```

**Formato del array:**
- Clave: tipo de área (es el parámetro en la URL)
- Valor: [nombre en BD, título a mostrar]

### ¡Listo!
Ya es accesible en: `/areas/nueva-area`

**Ventaja:** No necesitas crear vistas adicionales. La vista genérica (`areas/show.blade.php`) se reutiliza para todas las áreas.

---

## Características del Componente

El componente `medicamentos-table` incluye:

✅ **Estado de Vencimiento**
- Verde: Normal
- Amarillo: Pronto a vencer (< 20 días)
- Rojo: Vencido

✅ **Indicadores de Stock**
- Bajo Stock: Si stock < 5 y > 0
- Badge "Controlado" si aplica

✅ **Botón Deshabilitado Si:**
- Stock < 1
- Medicamento está vencido

✅ **DataTables**
- Paginación automática (8 registros por página)
- Búsqueda en tiempo real
- Exportar a Excel, PDF, Imprimir

---

## Cambios Realizados

1. ✅ Extracción de lógica de tabla en componente reutilizable
2. ✅ Creación de layout base dinámico
3. ✅ Consolidación a **una vista genérica** (`areas/show.blade.php`)
4. ✅ Centralización de lógica en método genérico `showAreaMedicamentos()`
5. ✅ Eliminación de métodos redundantes (botiquinList, carroList, maletinList)
6. ✅ Implementación de **ruta dinámica única** con configuración centralizada en array
7. ✅ Actualización de URLs en home.blade.php a nueva ruta
8. ✅ Corrección de lógica de fechas vencidas (usando `isPast()`)

---

## 🚀 Arquitectura Final

### Flujo de Ejecución

```
GET /areas/{areaType}
  ↓
Route::get('areas/{areaType}', [AreaController::class, 'showArea'])
  ↓
showArea($areaType)
  ├─ Obtiene config del array getAreaConfig()[$areaType]
  ├─ Extrae [areaName, titulo]
  └─ Llama showAreaMedicamentos($areaName, 'areas.show', $titulo)
      ├─ Ejecuta query (join con areas y farmacos)
      ├─ Pasa $areas y $titulo a la vista
      └─ Retorna vista genérica areas/show.blade.php
```

### Stack de Archivos

**AreaController.php:**
- `getAreaConfig()` — Array centralizado [areaName, titulo]
- `showArea($areaType)` — Método único que maneja todas las áreas
- `showAreaMedicamentos()` — Query base reutilizada

**routes/web.php:**
- `Route::get('areas/{areaType}', ...)` — Ruta dinámica única

**Vistas:**
- **Una sola vista genérica**: `areas/show.blade.php`
- Recibe `$titulo` dinámicamente desde el controlador
- El título aparece en el `@section('title', 'Farmacos ' . $titulo)`
- Componente `medicamentos-table.blade.php` renderiza la tabla

### Ventajas

- ✅ **Una línea** por nueva área (solo en el array)
- ✅ **Una vista única** para todas las áreas
- ✅ **Cero cambios en rutas** después del setup
- ✅ **Escalable infinitamente**
- ✅ **DRY** — Lógica reutilizada 100%
- ✅ **Mantenible** — Cambios afectan automáticamente todas las áreas

---

## Ejemplo Práctico: Agregar "Farmacia de Emergencia"

### Agregar línea en AreaController:
En el método `getAreaConfig()`:
```php
'farmacia-emergencia' => ['farmacia emergencia', 'Farmacia Emergencia'],
```

### ¡Listo!
Ya es accesible en: `/areas/farmacia-emergencia`

No necesitas:
- ❌ Crear vistas
- ❌ Crear métodos en el controlador
- ❌ Crear rutas nuevas
- ❌ Actualizar links

---

## Notas Técnicas

- Las vistas ahora son **minimalistas** (solo 3 líneas cada una)
- El componente puede **extenderse** fácilmente sin modificar vistas
- La lógica de DateTables y scripts está centralizada en el layout
- Los medicamentos se obtienen mediante **relación many-to-many** con validación por nombre de área
