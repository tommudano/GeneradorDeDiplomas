SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `alumnos`(
    id int(11) not null PRIMARY KEY AUTO_INCREMENT,
    nombre varchar(255) not null,
    apellido varchar(255) not null,
    dni varchar(255) not null UNIQUE
);

CREATE TABLE `cursos`(
    id int(11) not null PRIMARY KEY AUTO_INCREMENT,
    nombre varchar(255) not null UNIQUE,
    diplomaturaId int(11) not null
);

CREATE TABLE `profesores`(
    id int(11) not null PRIMARY KEY AUTO_INCREMENT,
    nombre varchar(255) not null,
    apellido varchar(255) not null,
    dni varchar(255) not null UNIQUE,
    firma blob not null
);

CREATE TABLE `diplomaturas`(
    id int(11) not null PRIMARY KEY AUTO_INCREMENT,
    nombre varchar(255) not null UNIQUE
);

CREATE TABLE `usuarios`(
    id int(11) not null PRIMARY KEY AUTO_INCREMENT,
    usuario varchar(255) not null UNIQUE,
    pwd varchar(255) not null,
    privilegio varchar(255) not null
);

CREATE TABLE `diplomas`(
    id int(11) not null PRIMARY KEY AUTO_INCREMENT,
    idAlumno int(11) not null,
    idCurso int(11) not null,
    idProfesor int(11) not null,
    mes varchar(20) not null,
    anio int(11) not null
);

ALTER TABLE diplomas
ADD FOREIGN KEY (idAlumno) REFERENCES alumnos(id),
ADD FOREIGN KEY (idProfesor) REFERENCES profesores(id),
ADD FOREIGN KEY (idCurso) REFERENCES cursos(id);

ALTER TABLE cursos
ADD FOREIGN KEY (diplomaturaId) REFERENCES diplomaturas(id);

INSERT INTO usuarios (usuario, pwd, privilegio) VALUES ('admin', '$2y$10$CDbMM4QdPngeWlbb7U9KG.BAMz8oZJgWJpyQTCV4ZTCxBnvUngH0G', 'admin')

INSERT INTO usuarios (usuario, pwd, privilegio) VALUES ('standard', '$2y$10$c4FFdRWyZciLUX6xPWa5quMjTMOj/lRXXofdPpte/2lhCeDF1erwG', 'admin')