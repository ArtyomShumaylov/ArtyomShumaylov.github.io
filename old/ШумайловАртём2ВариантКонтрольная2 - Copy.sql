-- задача 1
-- #1
CREATE TABLE Group (
    id_group INT PRIMARY KEY,
    group_no VARCHAR(20) NOT NULL,
    course INT NOT NULL CHECK (course BETWEEN 1 AND 10),
    semester INT,
    specialty VARCHAR(100)
);

CREATE TABLE Subject (
    id_sub INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(2) CHECK (type IN ('лк', 'аб')),
    volume INT,
    id_group INT,
    FOREIGN KEY (id_group) REFERENCES Group(id_group)
);

CREATE TABLE Student (
    id_student INT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    gradebook_no INT NOT NULL,
    date_in DATE NOT NULL,
    phone VARCHAR(20),
    id_sub INT,
    id_group INT,
    FOREIGN KEY (id_sub) REFERENCES Subject(id_sub),
    FOREIGN KEY (id_group) REFERENCES Group(id_group)
);

CREATE TABLE Structure_group (
    id_student INT,
    id_group INT,
    PRIMARY KEY (id_student, id_group),
    FOREIGN KEY (id_student) REFERENCES Student(id_student),
    FOREIGN KEY (id_group) REFERENCES Group(id_group)
);

INSERT INTO Group VALUES (1, 'ГР-01', 1, 1, 'Информатика');
INSERT INTO Group VALUES (2, 'ГР-02', 2, 3, 'Математика');

INSERT INTO Subject VALUES (1, 'Базы данных', 'лк', 100, 1);
INSERT INTO Subject VALUES (2, 'Алгоритмы', 'аб', 80, 2);

INSERT INTO Student VALUES (1, 'Иванов Иван Иванович', 12345, TO_DATE('2023-09-01', 'YYYY-MM-DD'), '+79991234567', 1, 1);
INSERT INTO Student VALUES (2, 'Петров Петр Петрович', 12346, TO_DATE('2023-09-01', 'YYYY-MM-DD'), '+79991234568', 2, 2);

INSERT INTO Structure_group VALUES (1, 1);
INSERT INTO Structure_group VALUES (2, 2);

--Задача 2
-- #5
SELECT first_name, last_name, hire_date, salary, commission_pct
FROM employees
WHERE hire_date > TO_DATE('22.10.2005', 'DD.MM.YYYY')
ORDER BY hire_date;

--#6
SELECT e.last_name, j.job_title, d.department_name
FROM employees e
JOIN jobs j ON e.job_id = j.job_id
JOIN departments d ON e.department_id = d.department_id
JOIN locations l ON d.location_id = l.location_id
WHERE j.job_title = 'Programmer' AND l.city = 'Southlake';

-- #7
SELECT l.city, COUNT(d.department_id) as department_count
FROM locations l
JOIN departments d ON l.location_id = d.location_id
GROUP BY l.city
HAVING COUNT(d.department_id) > 1;

-- #8
SELECT e.*
FROM employees e
WHERE e.employee_id IN (
    SELECT DISTINCT m.manager_id
    FROM employees m
    JOIN employees s ON m.employee_id = s.manager_id
    WHERE m.hire_date > s.hire_date
);