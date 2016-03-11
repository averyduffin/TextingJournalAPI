CREATE TABLE textingjournal.`questionfrequency` ( 
`id` int(11) NOT NULL AUTO_INCREMENT,
`type`  varchar(240), 
PRIMARY KEY (`id`)
)

CREATE TABLE textingjournal.`users` ( 
`id` int(11) NOT NULL AUTO_INCREMENT,
`fullname`  varchar(240), 
`emailaddress` varchar(240),
`username` varchar(240), 
`password` varchar(240),
`phonenumber` varchar(240),
`timezone` DATE NOT NULL,

`profilepic` varchar(240),
`backgroundpic` varchar(240),

`questionfrequencyid` int(11) NOT NULL,
`isDeleted` int(2),
PRIMARY KEY (`id`),
FOREIGN KEY (questionfrequencyid) REFERENCES questionfrequency(id)
)



CREATE TABLE textingjournal.`question` ( 
`id` int(11) NOT NULL AUTO_INCREMENT,
`question`  varchar(240), 
PRIMARY KEY (`id`)
)

CREATE TABLE textingjournal.`entries` ( 
`id` int(11) NOT NULL AUTO_INCREMENT,
`date` DATE NOT NULL,
`text`  varchar(240),
`phonenumber`  varchar(240),
`questionid` int(11) NOT NULL,
`userid` int(11) NOT NULL,
`about` varchar(240),
`messageSid` varchar(240),
`smsid` varchar(240),
`accountsid` varchar(240),
`messagingservicesid` varchar(240),
`nummedia` int(11),
`isDeleted` int(2),
PRIMARY KEY (`id`),
FOREIGN KEY (questionid) REFERENCES questiondate(id),
FOREIGN KEY (userid) REFERENCES users(id)
)

CREATE TABLE textingjournal.`questiondate` ( 
`id` int(11) NOT NULL AUTO_INCREMENT,
`questionid`  varchar(240),
`date` DATE NOT NULL,
PRIMARY KEY (`id`),
FOREIGN KEY (questionid) REFERENCES question(id),
)

DELIMITER $$
CREATE PROCEDURE averyduffin_textingjournal.`UpdateUser`
(
IN id_i int(11),
IN fullname_i  varchar(240), 
IN emailaddress_i varchar(240),
IN username_i varchar(240), 
IN password_i varchar(240),
IN phonenumber_i varchar(240),
IN profilepic_i varchar(240),
IN backgroundpic_i varchar(240),
IN questionfrequencyid_i int(11) ,
IN about_i varchar(240)
)
BEGIN

  UPDATE averyduffin_textingjournal.users_dev 
  SET fullname=fullname_i,
  phonenumber=phonenumber_i,
  emailaddress= emailaddress_i, 
  username=username_i, 
  password=password_i, 
  timezone=NOW(), 
  questionfrequencyid=questionfrequencyid_i, 
  about=about_i, 
  profilepic=profilepic_i, 
  backgroundpic=backgroundpic_i 
  WHERE `id`=id_i;
  
  SELECT * FROM averyduffin_textingjournal.users_dev
  WHERE `id`=id_i;
END $$
DELIMITER ;