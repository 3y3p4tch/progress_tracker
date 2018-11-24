USE voodle
GO
CREATE TABLE instructors (userID int PRIMARY KEY IDENTITY(1,1), username varchar(20) NOT NULL UNIQUE, passwd varchar(32) NOT NULL)
GO
INSERT INTO instructors (username, passwd) VALUES ('admin', '21232f297a57a5a743894a0e4a801fc3')
GO
SELECT * FROM instructors
GO
CREATE TABLE sessions_ (session_id int NOT NULL PRIMARY KEY IDENTITY(1,1), session_name nvarchar(max), userID int FOREIGN KEY REFERENCES instructors(userID), session_key UNIQUEIDENTIFIER DEFAULT NEWID(), start_time datetime, duration int, end_time datetime, details nvarchar(max));
GO
CREATE TABLE questions (ques_id int NOT NULL PRIMARY KEY IDENTITY(1,1), question_no int, session_id int FOREIGN KEY REFERENCES sessions_(session_id), problem nvarchar(max), options nvarchar(max), [type] varchar(3), comments nvarchar(max) NOT NULL DEFAULT '[]', students_crossed int NOT NULL DEFAULT 0);
GO
CREATE TABLE students (LDAP int PRIMARY KEY, [name] varchar(20) NOT NULL, passwd varchar(32) NOT NULL, [session] int FOREIGN KEY REFERENCES sessions_(session_id), keys nvarchar(max) NOT NULL DEFAULT '[]')
GO
CREATE TABLE pings (ques_id int FOREIGN KEY REFERENCES questions(ques_id), LDAP int FOREIGN KEY REFERENCES students(LDAP), [message] nvarchar(max), reply_to int FOREIGN KEY REFERENCES students(LDAP) NOT NULL DEFAULT 1, [time] time DEFAULT CONVERT(time, CURRENT_TIMESTAMP))
GO

INSERT INTO students (LDAP, [name], passwd) VALUES (1, 'instructor', '21232f297a57a5a743894a0e4a801fc3')

INSERT INTO students (LDAP, [name], passwd) VALUES (170050004, 'yash', '21232f297a57a5a743894a0e4a801fc3')

INSERT INTO students (LDAP, [name], passwd) VALUES (170050048, 'manaswi', '21232f297a57a5a743894a0e4a801fc3')

INSERT INTO students (LDAP, [name], passwd) VALUES (170050001, 'temp', '21232f297a57a5a743894a0e4a801fc3')

INSERT INTO students (LDAP, [name], passwd) VALUES (170050059, 'bot', '21232f297a57a5a743894a0e4a801fc3')
SELECT * FROM students JOIN sessions_ ON students.session = sessions_.session_id