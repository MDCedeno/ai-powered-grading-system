-- Table: users
INSERT INTO users (id,name,email,password,role_id,active,created_at,updated_at) VALUES ('1','John Doe','john.doe@plmun.edu.ph','$2y$10$kYELyoMyhJuOB057oMbI/u7nbM50cl4h.Ov1KcTVB.SCLBHdmPJOK','1','1','2025-09-20 20:29:05','2025-09-20 20:29:05');
INSERT INTO users (id,name,email,password,role_id,active,created_at,updated_at) VALUES ('2','Jane Smith','jane.smith@plmun.edu.ph','$2y$10$t1D4Nkm0Lk.KedIkg9gIzeb0ILyEn/6yoeFiiwjkDDob0CChOye9u','2','1','2025-09-20 20:29:05','2025-09-20 20:29:05');
INSERT INTO users (id,name,email,password,role_id,active,created_at,updated_at) VALUES ('3','Alice Johnson','alice.johnson@plmun.edu.ph','$2y$10$BtgHbrGz8h8yPjZI8xMe0eDSs7LIc7vb/MT.PUCIlJCMrsmo9Haj6','3','1','2025-09-20 20:29:05','2025-09-20 20:29:05');
INSERT INTO users (id,name,email,password,role_id,active,created_at,updated_at) VALUES ('4','Bob Wilson','bob.wilson@plmun.edu.ph','$2y$10$WRfqDXTOzRbY35BwxejR0e3ySIr2rV/MGkdZhx26MfNji1JrtDeta','3','1','2025-09-20 20:29:05','2025-09-20 20:29:05');
INSERT INTO users (id,name,email,password,role_id,active,created_at,updated_at) VALUES ('5','Carol Davis','carol.davis@plmun.edu.ph','$2y$10$m20XjWvApxYAAslDUPXAauut3b5YXvIl2OzPIwHQ6gPDY206hXvyO','3','1','2025-09-20 20:29:05','2025-09-20 20:29:05');
INSERT INTO users (id,name,email,password,role_id,active,created_at,updated_at) VALUES ('6','Charlie Pogi','charlie.pogi@plmun.edu.ph','$2y$10$yO0dpt6AGKG7Ckq0rlFQQOCJ8RtszPpXnkclvDqKRqSlg5eAUskke','4','1','2025-09-20 20:29:05','2025-09-20 20:29:05');
INSERT INTO users (id,name,email,password,role_id,active,created_at,updated_at) VALUES ('7','Diana Prince','diana.prince@plmun.edu.ph','$2y$10$XcLDj6pxSNq7Qu8gB0yqc.VqUeXfMSRJ10OSoeMHBqBKo.3tNRZda','4','1','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO users (id,name,email,password,role_id,active,created_at,updated_at) VALUES ('8','Edward Norton','edward.norton@plmun.edu.ph','$2y$10$GwsLAor8yJT6XyndrlPXLecC6rqmAI3WnMg9LW5eOSR/BNt.wex9O','4','1','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO users (id,name,email,password,role_id,active,created_at,updated_at) VALUES ('9','Fiona Green','fiona.green@plmun.edu.ph','$2y$10$MLh6gcrnLSidFkyYPvX8teM7SePdSiEmo7zB6T2uVbIV.PhBHn69C','4','1','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO users (id,name,email,password,role_id,active,created_at,updated_at) VALUES ('10','George Miller','george.miller@plmun.edu.ph','$2y$10$AycENPHF2yF6c/9TAP2P4ezJIFDyAPxKdMj6rGJ01iHtqJ/V0Zl.S','4','1','2025-09-20 20:29:06','2025-09-20 20:29:06');

-- Table: students
INSERT INTO students (id,user_id,program,year,created_at,updated_at) VALUES ('1','6','BSCS','4th Year','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO students (id,user_id,program,year,created_at,updated_at) VALUES ('2','7','BSIS','4th Year','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO students (id,user_id,program,year,created_at,updated_at) VALUES ('3','8','BSCS','2nd Year','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO students (id,user_id,program,year,created_at,updated_at) VALUES ('4','9','BSCS','2nd Year','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO students (id,user_id,program,year,created_at,updated_at) VALUES ('5','10','BSIT','2nd Year','2025-09-20 20:29:06','2025-09-20 20:29:06');

-- Table: courses
INSERT INTO courses (id,course_code,course_name,professor_id,semester,year,created_at,updated_at) VALUES ('1','CS101','Introduction to Computer Science','3','Fall 2024','2024','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO courses (id,course_code,course_name,professor_id,semester,year,created_at,updated_at) VALUES ('2','IT201','Database Management Systems','4','Fall 2024','2024','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO courses (id,course_code,course_name,professor_id,semester,year,created_at,updated_at) VALUES ('3','CS301','Data Structures and Algorithms','5','Spring 2024','2024','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO courses (id,course_code,course_name,professor_id,semester,year,created_at,updated_at) VALUES ('4','IT401','Web Development','3','Spring 2024','2024','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO courses (id,course_code,course_name,professor_id,semester,year,created_at,updated_at) VALUES ('5','CS501','Artificial Intelligence','4','Fall 2024','2024','2025-09-20 20:29:06','2025-09-20 20:29:06');

-- Table: grades
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('1','1','1','10','13','23','10','12','22','2.2','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('2','1','2','7','11','18','10','14','24','3.3','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('3','1','3','7','15','22','10','20','30','3.2','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('4','1','4','6','18','24','6','11','17','3.3','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('5','1','5','7','10','17','8','19','27','2.4','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('6','2','1','7','12','19','5','20','25','3.9','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('7','2','2','7','12','19','9','12','21','2.2','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('8','2','3','10','15','25','10','11','21','2.3','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('9','2','4','10','14','24','6','13','19','2.9','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('10','2','5','7','11','18','5','13','18','2.8','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('11','3','1','8','15','23','9','20','29','3.9','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('12','3','2','6','12','18','7','11','18','3.4','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('13','3','3','9','18','27','5','10','15','4','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('14','3','4','10','19','29','6','14','20','2.6','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('15','3','5','5','11','16','5','19','24','3.8','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('16','4','1','8','12','20','5','12','17','3','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('17','4','2','7','16','23','7','10','17','4','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('18','4','3','6','13','19','7','19','26','3.9','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('19','4','4','7','20','27','7','15','22','2.7','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('20','4','5','7','12','19','9','20','29','2.2','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('21','5','1','9','17','26','8','10','18','3.8','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('22','5','2','5','13','18','8','17','25','2.5','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('23','5','3','7','15','22','8','14','22','2.2','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('24','5','4','10','14','24','9','11','20','3.9','2025-09-20 20:29:06','2025-09-20 20:29:06');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('25','5','5','9','10','19','7','14','21','2.9','2025-09-20 20:29:06','2025-09-20 20:29:06');

-- Table: logs
INSERT INTO logs (id,user_id,log_type,action,details,success,failure_reason,created_at) VALUES ('1','1','login','Super admin logged in','','1','','2025-09-20 20:29:06');
INSERT INTO logs (id,user_id,log_type,action,details,success,failure_reason,created_at) VALUES ('2','2','create_course','MIS Admin created a new course','','1','','2025-09-20 20:29:06');
INSERT INTO logs (id,user_id,log_type,action,details,success,failure_reason,created_at) VALUES ('3','3','grade_submission','Professor graded student submission','','1','','2025-09-20 20:29:06');
INSERT INTO logs (id,user_id,log_type,action,details,success,failure_reason,created_at) VALUES ('4','6','view_grades','Student viewed their grades','','1','','2025-09-20 20:29:06');
INSERT INTO logs (id,user_id,log_type,action,details,success,failure_reason,created_at) VALUES ('5','1','system_backup','System backup completed successfully','','1','','2025-09-20 20:29:06');
INSERT INTO logs (id,user_id,log_type,action,details,success,failure_reason,created_at) VALUES ('6','1','login','Super admin logged in','','1','','2025-09-20 20:29:07');
INSERT INTO logs (id,user_id,log_type,action,details,success,failure_reason,created_at) VALUES ('7','2','create_course','MIS Admin created a new course','','1','','2025-09-20 20:29:07');
INSERT INTO logs (id,user_id,log_type,action,details,success,failure_reason,created_at) VALUES ('8','3','grade_submission','Professor graded student submission','','1','','2025-09-20 20:29:07');
INSERT INTO logs (id,user_id,log_type,action,details,success,failure_reason,created_at) VALUES ('9','6','view_grades','Student viewed their grades','','1','','2025-09-20 20:29:07');
INSERT INTO logs (id,user_id,log_type,action,details,success,failure_reason,created_at) VALUES ('10','1','system_backup','System backup completed successfully','','1','','2025-09-20 20:29:07');

