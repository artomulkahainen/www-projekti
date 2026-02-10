import os
import sys
import pymysql.cursors

DB_HOST = os.getenv("DB_HOST", "db")
DB_PORT = int(os.getenv("DB_PORT", 3306))
DB_NAME = os.getenv("DB_NAME", "mydb")
DB_USER = os.getenv("DB_USER", "myuser")
DB_PASS = os.getenv("DB_PASSWORD", "secret")

def main():
    try:
        conn = pymysql.connect(
            host=DB_HOST,
            port=DB_PORT,
            user=DB_USER,
            password=DB_PASS,
            database=DB_NAME,
            charset="utf8mb4",
            cursorclass=pymysql.cursors.DictCursor,
            autocommit=True,
        )
    except Exception as exc:
        print(f"❌ Could not connect to MySQL: {exc}", file=sys.stderr)
        sys.exit(1)

    try:
        with conn.cursor() as cur:
            cur.execute(
                """
                CREATE TABLE IF NOT EXISTS product_category (
                    id   INT AUTO_INCREMENT PRIMARY KEY,
                    name    VARCHAR(255) NOT NULL
                ) ENGINE=InnoDB;
                """
            )
                        
            cur.execute(
                """
                CREATE TABLE IF NOT EXISTS product (
                    id   INT AUTO_INCREMENT PRIMARY KEY,
                    name    VARCHAR(255) NOT NULL,
                    description VARCHAR(255),
                    image_url   VARCHAR(500) NOT NULL,
                    price   NUMERIC NOT NULL,
                    product_category_id INT NOT NULL,
                    CONSTRAINT fk_product_product_category
                        FOREIGN KEY (product_category_id) REFERENCES product_category(id)
                ) ENGINE=InnoDB;
                """
            )

            cur.execute(
                """
                CREATE TABLE IF NOT EXISTS product_review (
                    id   INT AUTO_INCREMENT PRIMARY KEY,
                    rating    INT NOT NULL,
                    product_id  INT NOT NULL,
                    CONSTRAINT fk_product_review_product
                        FOREIGN KEY (product_id) REFERENCES product(id)
                ) ENGINE=InnoDB;
                """
            )

            print(f"🚀 Migration applied successfully.")
    except Exception as exc:
        print(f"❌ Migration failed: {exc}", file=sys.stderr)
        sys.exit(1)
    finally:
        conn.close()


if __name__ == "__main__":
    main()