# Repository Guidelines (ndk-newdesertking)

## Database Schema & Migrations
- **Prisma is the Single Source of Truth**: All database schema changes, table definitions, and column types must be defined in `prisma/schema.prisma`.
- **No Laravel Migrations by Default**: Do not generate or create Laravel database migration files (`database/migrations/`) unless explicitly instructed by the user.
- **Model Alignments**: When modifying fields or data types in `prisma/schema.prisma`, update the corresponding Eloquent model attributes, `$fillable`, and `$casts` to match.

## Currency & Monetary Precision
- **Currency Standard**: All monetary values, prices, rates, and totals across the system are in **Omani Rial** (standard symbols/labels: **`R.O.`** or **`OMR`**). Never use other currency symbols like `Rs` or `$`.
- **3-Decimal Precision**: Display, format, and calculate all prices, rates, amounts, and totals with **3 decimal places** (e.g., `12.500`, `0.250`).
- **Database & Schema Invariant**: Define all monetary columns in `prisma/schema.prisma` as `Decimal @db.Decimal(10, 3)` and cast them as `'decimal:3'` in corresponding Eloquent models.
