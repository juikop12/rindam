#!/usr/bin/env bash
# ==============================================================================
# Skrip Backup Otomatis Database & Uploads SIPANDU-WBK
# Simpan di: /var/www/sipandu/deploy/backup-database.sh
# Jalankan harian via Cron: 0 2 * * * /var/www/sipandu/deploy/backup-database.sh
# ==============================================================================

set -euo pipefail

BACKUP_DIR="/var/backups/sipandu"
PROJECT_DIR="/var/www/sipandu"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
RETENTION_DAYS=14

mkdir -p "${BACKUP_DIR}"

echo "[$(date)] Memulai pencadangan SIPANDU..."

# 1. Backup SQLite (Jika menggunakan SQLite)
if [ -f "${PROJECT_DIR}/database/database.sqlite" ]; then
    DB_BACKUP_FILE="${BACKUP_DIR}/sipandu_db_${TIMESTAMP}.sqlite"
    if command -v sqlite3 >/dev/null 2>&1; then
        # Gunakan API resmi .backup agar konsisten walau sedang ada transaksi aktif (WAL mode)
        sqlite3 "${PROJECT_DIR}/database/database.sqlite" ".backup '${DB_BACKUP_FILE}'"
    else
        cp "${PROJECT_DIR}/database/database.sqlite" "${DB_BACKUP_FILE}"
    fi
    gzip -f "${DB_BACKUP_FILE}"
    echo "[OK] Database SQLite berhasil dicadangkan: ${DB_BACKUP_FILE}.gz"
fi

# 2. Backup Berkas Upload Pengguna (storage/app/public)
if [ -d "${PROJECT_DIR}/storage/app/public" ]; then
    MEDIA_BACKUP_FILE="${BACKUP_DIR}/sipandu_media_${TIMESTAMP}.tar.gz"
    tar -czf "${MEDIA_BACKUP_FILE}" -C "${PROJECT_DIR}/storage/app" public 2>/dev/null || true
    echo "[OK] Media/Uploads berhasil dicadangkan: ${MEDIA_BACKUP_FILE}"
fi

# 3. Rotasi Berkas (Hapus backup yang berusia lebih dari 14 hari)
find "${BACKUP_DIR}" -type f -name "sipandu_*.gz" -mtime +${RETENTION_DAYS} -delete
echo "[OK] Pembersihan file cadangan usang (> ${RETENTION_DAYS} hari) selesai."
echo "[$(date)] Pencadangan selesai dengan sukses!"
