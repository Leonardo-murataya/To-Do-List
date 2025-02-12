CREATE DATABASE ToDoList;

USE ToDoList;

-- Tabla de usuarios
CREATE TABLE Usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password CHAR(60) NOT NULL
);

-- Tabla de listas
CREATE TABLE listas (
    id BIGINT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    usuario_id INT NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabla de tareas
CREATE TABLE tareas (
    id BIGINT PRIMARY KEY,
    texto TEXT NOT NULL,
    completada BOOLEAN DEFAULT FALSE,
    lista_id BIGINT NOT NULL,
    FOREIGN KEY (lista_id) REFERENCES listas(id)
);

-- para cambiar el campo password de varchar(255) a char(60) para que sea compatible con la encriptación de password
-- ALTER TABLE usuarios MODIFY COLUMN password CHAR(60) NOT NULL;