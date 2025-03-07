# Sistema ERP para Construcción

Un sistema de Planificación de Recursos Empresariales completo diseñado específicamente para empresas de construcción que permite gestionar empleados, proyectos, tareas y presupuestos de manera eficiente.

![Sistema ERP para Construcción](https://via.placeholder.com/800x400?text=Sistema+ERP+Construccion)

## Descripción General

Este Sistema ERP para Construcción proporciona una solución completa para que las empresas de construcción gestionen sus recursos, proyectos y finanzas. El sistema cuenta con una interfaz amigable construida con Vue.js y Tailwind CSS, respaldada por una robusta arquitectura PHP MVC, permitiendo a los gerentes de construcción y empleados optimizar sus operaciones y mejorar la productividad.

## Características

### Gestión de Empleados
- Añadir, editar y eliminar perfiles de empleados
- Gestionar roles y permisos de empleados
- Seguimiento de disponibilidad y asignaciones de empleados
- Monitoreo del rendimiento de los empleados

### Gestión de Proyectos
- Crear y gestionar proyectos de construcción
- Seguimiento del estado, plazos y fechas límite de los proyectos
- Asignar personal responsable a los proyectos
- Monitoreo del progreso del proyecto

### Gestión de Tareas
- Crear, asignar y hacer seguimiento de tareas
- Vincular tareas a proyectos específicos
- Establecer prioridades, plazos y dependencias de tareas
- Monitoreo de finalización y rendimiento de tareas

### Gestión de Presupuestos
- Crear y gestionar presupuestos de proyectos
- Seguimiento de gastos y costos
- Generar informes financieros
- Monitoreo de variaciones presupuestarias

## Stack Tecnológico

### Frontend
- **Framework**: Vue.js 3
- **Estilizado**: Tailwind CSS
- **Herramienta de construcción**: Vite
- **Gestión de estado**: Vue Router para navegación
- **Cliente HTTP**: Axios para peticiones API
- **Visualización de datos**: Chart.js

### Backend
- **Lenguaje**: PHP 8
- **Arquitectura**: MVC (Modelo-Vista-Controlador)
- **API**: Endpoints RESTful API
- **Autenticación**: JWT (JSON Web Tokens)

### Base de Datos
- Base de datos **MySQL** para almacenamiento de datos
- Diseño de base de datos relacional con tablas para empleados, proyectos, tareas y presupuestos

### Despliegue
- Containerización con **Docker** para fácil despliegue y escalabilidad
- Docker Compose para orquestar configuración multi-contenedor

## Estructura del Proyecto

```
sistema-erp-construccion/
├── backend/                  # Backend PHP MVC
│   ├── src/
│   │   ├── Controller/       # Controladores API
│   │   ├── Model/            # Modelos de base de datos
│   │   ├── Service/          # Lógica de negocio
│   │   └── Router.php        # Enrutamiento API
│   └── Dockerfile            # Configuración de contenedor backend
├── frontend/                 # Frontend Vue.js
│   └── sistema-erp-construccion/
│       ├── src/
│       │   ├── components/   # Componentes UI reutilizables
│       │   ├── views/        # Componentes de páginas
│       │   ├── router/       # Configuración de Vue Router
│       │   └── assets/       # Activos estáticos
│       ├── tailwind.config.js # Configuración de Tailwind CSS
│       └── package.json      # Dependencias del frontend
└── db/                       # Scripts de base de datos
    └── sistemaERP.sql        # Esquema de base de datos y datos iniciales
```

## Instrucciones de Configuración

### Requisitos Previos
- Docker y Docker Compose instalados
- Git para clonar el repositorio

### Instalación

1. Clonar el repositorio:
   ```bash
   git clone https://github.com/yourusername/sistema-erp-construccion.git
   cd sistema-erp-construccion
   ```

2. Construir e iniciar los contenedores Docker:
   ```bash
   docker-compose up -d
   ```

3. La aplicación estará disponible en:
   - Frontend: http://localhost:8080
   - API Backend: http://localhost:9000

### Configuración de Base de Datos

La base de datos se inicializará automáticamente con el esquema y los datos iniciales de `db/sistemaERP.sql`.

## Uso

### Usuarios Administradores
1. Inicie sesión con credenciales de administrador
2. Acceda al panel de administración
3. Gestione empleados, proyectos, tareas y presupuestos
4. Genere informes y analice el rendimiento

### Usuarios Regulares
1. Inicie sesión con sus credenciales de empleado
2. Vea tareas y proyectos asignados
3. Actualice el estado y progreso de las tareas
4. Vea métricas de rendimiento personal

## Contribuir

1. Haga un fork del repositorio
2. Cree una rama de características (`git checkout -b feature/caracteristica-asombrosa`)
3. Confirme sus cambios (`git commit -m 'Añadir alguna característica asombrosa'`)
4. Haga push a la rama (`git push origin feature/caracteristica-asombrosa`)
5. Abra una Solicitud de Extracción (Pull Request)

## Licencia

Este proyecto está licenciado bajo la Licencia MIT - vea el archivo LICENSE para más detalles.

## Contacto

Enlace del Proyecto: [https://github.com/yourusername/sistema-erp-construccion](https://github.com/yourusername/sistema-erp-construccion)

