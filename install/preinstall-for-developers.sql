CREATE DATABASE  `chess` ;
CREATE USER 'chessuser'@'localhost' IDENTIFIED BY  '***';

-- Please use this user with the password "localpass" to avoid submitting your
-- private local username / password
GRANT ALL PRIVILEGES ON * . * TO  'chessuser'@'localhost' IDENTIFIED BY  '***' WITH GRANT OPTION MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;
