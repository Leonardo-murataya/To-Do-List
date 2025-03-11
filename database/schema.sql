-- Create database
CREATE DATABASE IF NOT EXISTS ToDoList;
USE ToDoList;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS Usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password CHAR(60) NOT NULL
);

-- Tabla de listas
CREATE TABLE IF NOT EXISTS listas (
    id BIGINT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    usuario_id INT NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES Usuarios(id)
);

-- Tabla de tareas
CREATE TABLE IF NOT EXISTS tareas (
    id BIGINT PRIMARY KEY,
    texto TEXT NOT NULL,
    completada BOOLEAN DEFAULT FALSE,
    lista_id BIGINT NOT NULL,
    FOREIGN KEY (lista_id) REFERENCES listas(id)
);