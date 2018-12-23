/*
SQL Statistics: MIN, MEDIAN, MAX
https://www.codewars.com/kata/sql-statistics-min-median-max
*/

SELECT min, ((p1 + p2) / 2)::FLOAT AS median, max FROM (
  SELECT
    MIN(score) AS min,
    PERCENTILE_DISC(0.5) WITHIN GROUP (ORDER BY score) AS p1,
    PERCENTILE_DISC(0.51) WITHIN GROUP (ORDER BY score) AS p2,
    MAX(score) AS max
  FROM result
) as aggregated
