# Конфигурация

Конфигурация бандла полностью повторяет конфигуацию пакета  [spiral/dbal](https://spiral.dev/docs/database-configuration)

Конфиг:

```yaml
spiral_database:
  default: default
  aliases:
    default: primary
    database: primary
    db: primary
  databases:
    primary:
      connection: mysql
      tablePrefix: ''

  connections:
    mysql:
      driver: mysql
      options:
        connection: mysql:host=127.0.0.1;dbname=test
        username: test
        password: test
```