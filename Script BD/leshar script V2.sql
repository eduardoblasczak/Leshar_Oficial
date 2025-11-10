CREATE DATABASE IF NOT EXISTS Leshar_Oficial;
USE Leshar_Oficial;


CREATE TABLE IF NOT EXISTS adm (
  idadm INT NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (idadm)
);

CREATE TABLE IF NOT EXISTS usuario (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL,
  tipo_usuario ENUM('USUARIO', 'ADM') DEFAULT 'USUARIO',
  credito INT DEFAULT 0,
  bio TEXT,
  localizacao VARCHAR(250),
  data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS aluno (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS mentor (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  reputacao INT DEFAULT 100,
  FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS categoria_habilidade (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS habilidade (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  descricao TEXT,
  categoria_id INT NOT NULL,
  FOREIGN KEY (categoria_id) REFERENCES categoria_habilidade(id) ON DELETE CASCADE
  );

CREATE TABLE IF NOT EXISTS habilidade_desejada (
	id INT AUTO_INCREMENT PRIMARY KEY,
    habilidade_id INT NOT NULL,
    FOREIGN KEY (habilidade_id) REFERENCES habilidade(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS aluno_habilidade_desejada (
   aluno_id INT NOT NULL,
   habilidade_desejada_id INT NOT NULL,
   PRIMARY KEY (aluno_id, habilidade_desejada_id),
   FOREIGN KEY (aluno_id) REFERENCES aluno(id) ON DELETE CASCADE,
   FOREIGN KEY (habilidade_desejada_id) REFERENCES habilidade_desejada(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS habilidade_ofertada(
	id INT AUTO_INCREMENT PRIMARY KEY,
    habilidade_id INT NOT NULL,
    FOREIGN KEY (habilidade_id) REFERENCES habilidade(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS mentor_habilidade_ofertada(
	mentor_id INT NOT NULL,
    habilidade_ofertada_id INT NOT NULL,
    PRIMARY KEY (mentor_id, habilidade_ofertada_id),
    FOREIGN KEY (mentor_id) REFERENCES mentor(id) ON DELETE CASCADE,
    FOREIGN KEY (habilidade_ofertada_id) REFERENCES habilidade_ofertada(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS aula (
  id INT AUTO_INCREMENT PRIMARY KEY,
  hora_inicio DATETIME NOT NULL,
  hora_fim DATETIME NOT NULL,
  mensagem JSON DEFAULT NULL
);



CREATE TABLE IF NOT EXISTS participante_aula (
  id INT AUTO_INCREMENT PRIMARY KEY,
  aula_id INT NOT NULL,
  mentor_id INT NOT NULL,
  aluno_id INT NOT NULL,
  FOREIGN KEY (aula_id) REFERENCES aula(id) ON DELETE CASCADE,
  FOREIGN KEY (mentor_id) REFERENCES mentor(id) ON DELETE CASCADE,
  FOREIGN KEY (aluno_id) REFERENCES aluno(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS avaliacao (
	id INT AUTO_INCREMENT PRIMARY KEY,
    pontuacao DECIMAL(3,1) NOT NULL,
    data DATE NOT NULL,
    participante_aula_id INT NOT NULL,
    FOREIGN KEY(participante_aula_id) REFERENCES participante_aula(id) ON DELETE CASCADE
);



CREATE TABLE IF NOT EXISTS transferencia_credito (
	id INT AUTO_INCREMENT PRIMARY KEY,
    quantidade INT NOT NULL,
    data DATE NOT NULL,
    aula_id INT NOT NULL,
    aluno_id INT NOT NULL,
    FOREIGN KEY (aula_id) REFERENCES aula(id) ON DELETE CASCADE,
    FOREIGN KEY (aluno_id) REFERENCES aluno(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS chat_message (
    id INT AUTO_INCREMENT PRIMARY KEY,
    remetente_id INT NOT NULL, 
    destinatario_id INT NOT NULL, 
    mensagem TEXT NOT NULL, 
    data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    FOREIGN KEY (remetente_id) REFERENCES usuario(id) ON DELETE CASCADE,
    FOREIGN KEY (destinatario_id) REFERENCES usuario(id) ON DELETE CASCADE
);

ALTER TABLE aula 
MODIFY COLUMN mensagem TEXT;

INSERT INTO usuario(nome, email, senha, tipo_usuario)
VALUES 
('ADM', 'adm@adm.com', 'adm', 'ADM'); 
