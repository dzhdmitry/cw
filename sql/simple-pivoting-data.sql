/*
SQL Basics: Simple PIVOTING data
https://www.codewars.com/kata/sql-basics-simple-pivoting-data
*/

CREATE EXTENSION tablefunc;

SELECT * FROM crosstab(
  'SELECT products.name, details.detail, COUNT(details.detail) as amount
  FROM products
  INNER JOIN details ON details.product_id = products.id
  GROUP BY 1, 2
  ORDER BY 1, 2',

  'SELECT DISTINCT(details.detail) FROM details'
)
AS products(name VARCHAR, bad BIGINT, ok BIGINT, good BIGINT);
