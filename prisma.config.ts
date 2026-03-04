import "dotenv/config";
import { defineConfig } from "@prisma/config";

export default defineConfig({
  schema: "prisma/schema.prisma",
  // The '!' tells TypeScript that DATABASE_URL is guaranteed to be a string.
  // This satisfies the 'exactOptionalPropertyTypes' constraint.
  datasource: {
    url: process.env.DATABASE_URL!,
  },
});