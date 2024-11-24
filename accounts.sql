CREATE TABLE accounts (
  usersId int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
  usersName varchar(128) NOT NULL,
  usersPassword varchar(128) NOT NULL,
  usersEmail varchar(128) NOT NULL,
  usersVkey varchar(128) NOT NULL,
  usersVerified varchar(128) NOT NULL,
  usersCodePwChange varchar(128) NOT NULL
);