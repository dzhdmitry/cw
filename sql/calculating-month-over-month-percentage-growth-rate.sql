/* Calculating Month-Over-Month Percentage Growth Rate */

SELECT
  current.date,
  current.count,
  CASE WHEN next.count > 0 THEN
    (ROUND(CAST(current.count::FLOAT / (next.count::FLOAT / 100.0) AS NUMERIC), 1) - 100)::TEXT || '%'
  ELSE
    NULL
END AS percent_growth
FROM (
  SELECT date_trunc('month', created_at)::date as date, COUNT(*) as count
  FROM posts
  GROUP BY date
  ORDER BY date
) as current
LEFT JOIN (
  SELECT date_trunc('month', created_at)::date as date, COUNT(*) as count
  FROM posts
  GROUP BY date
  ORDER BY date
) as next ON current.date = next.date + INTERVAL '1 month'
