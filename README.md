The HRIS Absensi application is fully built and operational. Here's a summary:

Application Summary
Login Credentials:

Role	Email	Password
Super Admin	superadmin@absen.app	password
HR	hr1@absen.app / hr2@absen.app	password
Manager	manager1-5@absen.app	password
Employee	employee1-50@absen.app	password
Database Statistics:

58 Users & Employees, 10 Departments, 81 Positions
5 Shifts, 1334 Attendance Records, 125 Leave Requests, 109 Overtime
4 Roles, 51 Permissions
Architecture:

MVC + Service Layer + Repository Pattern
Clean Architecture with SOLID principles
Dynamic RBAC via Spatie Permission
Activity Logging via Spatie Activitylog
Tabler UI with dark mode support
REST API with Sanctum auth
Full CRUD for all modules with export/import
Key Features:

Attendance check-in/out with GPS + selfie
Shift management (fixed/flexible/rotating)
Leave & Overtime management with approval workflow
Real-time dashboard with charts (ApexCharts)
Dynamic sidebar based on user role
Notification system (database + email-ready)
Reporting (PDF + Excel)
Soft delete & restore
Audit trail & activity logs
Dark/light mode toggle