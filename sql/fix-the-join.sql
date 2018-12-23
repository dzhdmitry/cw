/* SQL Bug Fixing: Fix the JOIN */

SELECT job_title,
  AVG(salary)::NUMERIC(6, 2)::FLOAT AS average_salary,
  COUNT(people.id) AS total_people,
  SUM(salary)::NUMERIC(6, 2)::FLOAT AS total_salary
FROM job
  LEFT JOIN people ON people.id = job.people_id
GROUP BY job_title
ORDER BY average_salary DESC
