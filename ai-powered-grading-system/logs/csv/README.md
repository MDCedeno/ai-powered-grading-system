# CSV Logs Storage Structure

This directory contains the CSV export files for audit logs and system logs.

## Directory Structure

```
logs/csv/
├── README.md                    # This file
├── archive/                     # Archived CSV files (older than retention period)
├── failed/                      # Failed export attempts
├── processing/                  # Currently processing exports
├── settings/                    # Configuration files for CSV logging
├── 2024-12/                     # December 2024 logs
├── 2025-01/                     # January 2025 logs
├── 2025-02/                     # February 2025 logs
└── 2025-09/                     # Current month logs
```

## File Naming Convention

CSV files follow this naming pattern:
- `audit_logs_YYYY-MM-DD_HH-MM-SS.csv` - Daily audit log exports
- `system_logs_YYYY-MM-DD_HH-MM-SS.csv` - System log exports
- `failed_export_YYYY-MM-DD_HH-MM-SS.log` - Failed export logs

## Directory Purposes

- **archive/**: Contains CSV files that have exceeded the retention period
- **failed/**: Contains logs of failed export operations
- **processing/**: Temporary location for CSV files being generated
- **settings/**: Contains configuration files for CSV logging settings
- **YYYY-MM/**: Monthly organization of CSV files by date

## Retention Policy

- Default retention period: 30 days
- Files older than retention period are moved to archive/
- Archive files are automatically cleaned up based on settings
- Failed exports are kept for 7 days for debugging

## CSV File Format

All CSV files include these standard columns:
- timestamp: ISO 8601 format (YYYY-MM-DD HH:MM:SS)
- user: Username or system identifier
- log_type: Type of log entry
- action: Action performed
- details: Additional details
- status: Success/Failed
- failure_reason: Reason for failure (if applicable)

## Access Control

- CSV files should be accessible only to super admin users
- Regular users should not have direct access to these files
- File permissions should be set to 644 (readable by all, writable by owner)
- Directory permissions should be set to 755

## Maintenance

- Regular cleanup of old files should be performed
- Monitor disk usage in this directory
- Backup important CSV files to external storage
- Archive old files to reduce disk usage
