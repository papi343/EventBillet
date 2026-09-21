import os
import re
import mysql.connector

def parse_env_file(env_path):
    env_vars = {}
    if not os.path.exists(env_path):
        return env_vars

    with open(env_path, 'r', encoding='utf-8') as f:
        for line in f:
            line = line.strip()
            if not line or line.startswith('#'):
                continue
            if '=' in line:
                key, val = line.split('=', 1)
                env_vars[key.strip()] = val.strip().strip('"').strip("'")
    return env_vars

def update_env_file(env_path, db_name):
    if not os.path.exists(env_path):
        print(f"Fichier {env_path} non trouve.")
        return

    with open(env_path, 'r', encoding='utf-8') as f:
        content = f.read()

    # Décommenter et mettre à jour ou ajouter les clés MySQL
    replacements = {
        r'^#?\s*DB_CONNECTION=.*': 'DB_CONNECTION=mysql',
        r'^#?\s*DB_HOST=.*': 'DB_HOST=127.0.0.1',
        r'^#?\s*DB_PORT=.*': 'DB_PORT=3306',
        r'^#?\s*DB_DATABASE=.*': f'DB_DATABASE={db_name}',
        r'^#?\s*DB_USERNAME=.*': 'DB_USERNAME=root',
        r'^#?\s*DB_PASSWORD=.*': 'DB_PASSWORD=',
    }

    new_content = content
    for pattern, replacement in replacements.items():
        if re.search(pattern, new_content, flags=re.MULTILINE):
            new_content = re.sub(pattern, replacement, new_content, flags=re.MULTILINE)
        else:
            new_content += f"\n{replacement}"

    with open(env_path, 'w', encoding='utf-8') as f:
        f.write(new_content)

    print(f"[OK] Le fichier .env a ete mis a jour avec DB_DATABASE={db_name} et configuration MySQL.")

def create_mysql_database():
    env_path = os.path.join(os.path.dirname(__file__), '.env')
    env_vars = parse_env_file(env_path)

    host = env_vars.get('DB_HOST', '127.0.0.1')
    user = env_vars.get('DB_USERNAME', 'root')
    password = env_vars.get('DB_PASSWORD', '')
    port = int(env_vars.get('DB_PORT', 3306))
    db_name = env_vars.get('DB_DATABASE', 'eventbillet')

    if db_name == 'laravel' or not db_name:
        db_name = 'eventbillet'

    print(f"Connexion au serveur MySQL ({host}:{port}) avec l'utilisateur '{user}'...")

    try:
        connection = mysql.connector.connect(
            host=host,
            user=user,
            password=password,
            port=port
        )

        cursor = connection.cursor()
        cursor.execute(f"CREATE DATABASE IF NOT EXISTS `{db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;")
        print(f"[OK] Base de donnees MySQL '{db_name}' creee ou deja existante avec succes !")

        cursor.close()
        connection.close()

        # Mettre à jour le fichier .env
        update_env_file(env_path, db_name)

    except mysql.connector.Error as err:
        print(f"[ERR] Erreur lors de la connexion ou creation de la base de donnees : {err}")

if __name__ == '__main__':
    create_mysql_database()

