-- Table: users
INSERT INTO users (id,name,email,password,role_id,active,created_at,updated_at) VALUES ('1','John Doe','john.doe@plmun.edu.ph','$2y$10$kybXtUcyqF5UusCrcIZFperLgXzIwE7pPnHtP.teotVQKFYdO5/hK','1','1','2025-09-18 17:26:53','2025-09-18 17:26:53');
INSERT INTO users (id,name,email,password,role_id,active,created_at,updated_at) VALUES ('2','Jane Smith','jane.smith@plmun.edu.ph','$2y$10$ON/1Kj9hRBMmS77gV9QXue.dfs3U2l8pXuA16/pX2O0TntZ190fji','2','1','2025-09-18 17:26:53','2025-09-18 17:26:53');
INSERT INTO users (id,name,email,password,role_id,active,created_at,updated_at) VALUES ('3','Alice Johnson','alice.johnson@plmun.edu.ph','$2y$10$5oPfQhIJmCrpRLSpcy7rXOUkSuuTzhBOxRkF3ajCVDLlKjg1vUPQy','3','1','2025-09-18 17:26:53','2025-09-18 17:26:53');
INSERT INTO users (id,name,email,password,role_id,active,created_at,updated_at) VALUES ('4','Bob Wilson','bob.wilson@plmun.edu.ph','$2y$10$BTXAh7Ujm3jVxSsybfcjV.7pRgFyTcAvFU5.cjxq5Ntk2v9gc3tH6','3','1','2025-09-18 17:26:53','2025-09-18 17:26:53');
INSERT INTO users (id,name,email,password,role_id,active,created_at,updated_at) VALUES ('5','Carol Davis','carol.davis@plmun.edu.ph','$2y$10$bpbOYjzDvKdUvRmIZq5wcOphHSLIpN1ZiVjVOarbhKrMzrgPZZnYW','3','1','2025-09-18 17:26:53','2025-09-18 17:26:53');
INSERT INTO users (id,name,email,password,role_id,active,created_at,updated_at) VALUES ('6','Charlie Brown','charlie.brown@plmun.edu.ph','$2y$10$CpdUpwN0DRukfNYf7143ne2n7UshpLBlf9Kw14.Ksqsf2gwkO1LXG','4','0','2025-09-18 17:26:53','2025-09-18 17:38:56');
INSERT INTO users (id,name,email,password,role_id,active,created_at,updated_at) VALUES ('7','Diana Prince','diana.prince@plmun.edu.ph','$2y$10$.Oz0Lm2viOiJ90tZzv5GKugs2AsdVmD5hAW0QcBYJJk6YlxSLCon6','4','1','2025-09-18 17:26:53','2025-09-18 17:26:53');
INSERT INTO users (id,name,email,password,role_id,active,created_at,updated_at) VALUES ('8','Edward Norton','edward.norton@plmun.edu.ph','$2y$10$Uyd3YNJAvUfjCKD/eamZUO8LZz3ag/u6lZgBazV3WQIUkoB3xvLvu','4','1','2025-09-18 17:26:53','2025-09-18 17:26:53');
INSERT INTO users (id,name,email,password,role_id,active,created_at,updated_at) VALUES ('9','Fiona Green','fiona.green@plmun.edu.ph','$2y$10$D/tElac.LUtrkCmRTEQozuRx5ko6Q164TeKzQIM9ii3go9/oy/GH6','4','1','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO users (id,name,email,password,role_id,active,created_at,updated_at) VALUES ('10','George Miller','george.miller@plmun.edu.ph','$2y$10$UbHKmZZ.WRVKey4uQtzONuPekwMDZqK.imaKRimmRasVi/ejptj8y','4','1','2025-09-18 17:26:54','2025-09-18 17:26:54');

-- Table: students
INSERT INTO students (id,user_id,program,year,created_at,updated_at) VALUES ('1','6','BSIT','2nd Year','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO students (id,user_id,program,year,created_at,updated_at) VALUES ('2','7','BSIS','3rd Year','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO students (id,user_id,program,year,created_at,updated_at) VALUES ('3','8','BSCS','1st Year','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO students (id,user_id,program,year,created_at,updated_at) VALUES ('4','9','BSCS','1st Year','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO students (id,user_id,program,year,created_at,updated_at) VALUES ('5','10','BSIT','2nd Year','2025-09-18 17:26:54','2025-09-18 17:26:54');

-- Table: courses
INSERT INTO courses (id,course_code,course_name,professor_id,semester,year,created_at,updated_at) VALUES ('1','CS101','Introduction to Computer Science','3','Fall 2024','2024','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO courses (id,course_code,course_name,professor_id,semester,year,created_at,updated_at) VALUES ('2','IT201','Database Management Systems','4','Fall 2024','2024','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO courses (id,course_code,course_name,professor_id,semester,year,created_at,updated_at) VALUES ('3','CS301','Data Structures and Algorithms','5','Spring 2024','2024','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO courses (id,course_code,course_name,professor_id,semester,year,created_at,updated_at) VALUES ('4','IT401','Web Development','3','Spring 2024','2024','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO courses (id,course_code,course_name,professor_id,semester,year,created_at,updated_at) VALUES ('5','CS501','Artificial Intelligence','4','Fall 2024','2024','2025-09-18 17:26:54','2025-09-18 17:26:54');

-- Table: grades
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('1','1','1','10','10','20','10','14','24','3.8','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('2','1','2','7','15','22','5','14','19','3.4','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('3','1','3','10','13','23','9','20','29','2.5','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('4','1','4','8','20','28','7','10','17','3.5','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('5','1','5','6','19','25','7','13','20','3.9','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('6','2','1','6','16','22','6','15','21','2.1','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('7','2','2','10','17','27','6','12','18','2.1','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('8','2','3','6','13','19','8','20','28','3.7','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('9','2','4','6','14','20','7','15','22','3','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('10','2','5','6','15','21','7','17','24','2.2','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('11','3','1','5','13','18','8','11','19','3.9','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('12','3','2','10','10','20','8','16','24','4','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('13','3','3','6','11','17','6','15','21','2.7','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('14','3','4','5','16','21','8','17','25','3.8','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('15','3','5','9','13','22','5','10','15','3.9','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('16','4','1','7','11','18','7','20','27','2.4','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('17','4','2','6','20','26','8','18','26','3.9','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('18','4','3','5','13','18','6','12','18','3.6','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('19','4','4','10','15','25','10','14','24','2.4','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('20','4','5','9','10','19','9','12','21','3.1','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('21','5','1','6','15','21','7','12','19','3.1','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('22','5','2','10','17','27','8','14','22','3.9','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('23','5','3','8','19','27','10','17','27','2.5','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('24','5','4','9','20','29','6','12','18','2.7','2025-09-18 17:26:54','2025-09-18 17:26:54');
INSERT INTO grades (id,student_id,course_id,midterm_quizzes,midterm_exam,midterm_grade,final_quizzes,final_exam,final_grade,gpa,created_at,updated_at) VALUES ('25','5','5','10','17','27','5','15','20','3','2025-09-18 17:26:54','2025-09-18 17:26:54');

-- Table: logs
INSERT INTO logs (id,user_id,action,details,created_at) VALUES ('1','1','login','Super admin logged in','2025-09-18 17:26:54');
INSERT INTO logs (id,user_id,action,details,created_at) VALUES ('2','2','create_course','MIS Admin created a new course','2025-09-18 17:26:54');
INSERT INTO logs (id,user_id,action,details,created_at) VALUES ('3','3','grade_submission','Professor graded student submission','2025-09-18 17:26:54');
INSERT INTO logs (id,user_id,action,details,created_at) VALUES ('4','6','view_grades','Student viewed their grades','2025-09-18 17:26:54');
INSERT INTO logs (id,user_id,action,details,created_at) VALUES ('5','1','system_backup','System backup completed successfully','2025-09-18 17:26:54');
INSERT INTO logs (id,user_id,action,details,created_at) VALUES ('6','1','login','Super admin logged in','2025-09-18 17:26:54');
INSERT INTO logs (id,user_id,action,details,created_at) VALUES ('7','2','create_course','MIS Admin created a new course','2025-09-18 17:26:54');
INSERT INTO logs (id,user_id,action,details,created_at) VALUES ('8','3','grade_submission','Professor graded student submission','2025-09-18 17:26:54');
INSERT INTO logs (id,user_id,action,details,created_at) VALUES ('9','6','view_grades','Student viewed their grades','2025-09-18 17:26:54');
INSERT INTO logs (id,user_id,action,details,created_at) VALUES ('10','1','system_backup','System backup completed successfully','2025-09-18 17:26:54');
INSERT INTO logs (id,user_id,action,details,created_at) VALUES ('11','1','login','Super admin logged in','2025-09-18 17:28:53');
INSERT INTO logs (id,user_id,action,details,created_at) VALUES ('12','2','create_course','MIS Admin created a new course','2025-09-18 17:28:53');
INSERT INTO logs (id,user_id,action,details,created_at) VALUES ('13','3','grade_submission','Professor graded student submission','2025-09-18 17:28:53');
INSERT INTO logs (id,user_id,action,details,created_at) VALUES ('14','6','view_grades','Student viewed their grades','2025-09-18 17:28:53');
INSERT INTO logs (id,user_id,action,details,created_at) VALUES ('15','1','system_backup','System backup completed successfully','2025-09-18 17:28:53');
INSERT INTO logs (id,user_id,action,details,created_at) VALUES ('16','1','login','Super admin logged in','2025-09-18 17:28:53');
INSERT INTO logs (id,user_id,action,details,created_at) VALUES ('17','2','create_course','MIS Admin created a new course','2025-09-18 17:28:53');
INSERT INTO logs (id,user_id,action,details,created_at) VALUES ('18','3','grade_submission','Professor graded student submission','2025-09-18 17:28:53');
INSERT INTO logs (id,user_id,action,details,created_at) VALUES ('19','6','view_grades','Student viewed their grades','2025-09-18 17:28:53');
INSERT INTO logs (id,user_id,action,details,created_at) VALUES ('20','1','system_backup','System backup completed successfully','2025-09-18 17:28:53');

