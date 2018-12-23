/*
Challenge: Two actors who cast together the most
https://www.codewars.com/kata/challenge-two-actors-who-cast-together-the-most
*/

CREATE TEMPORARY VIEW top_actors AS SELECT fa1.actor_id AS a1, fa2.actor_id AS a2, COUNT(fa2.film_id) AS amount
  FROM film_actor fa1
    LEFT JOIN film_actor fa2 ON fa2.film_id = fa1.film_id AND fa2.actor_id <> fa1.actor_id
  WHERE fa2.actor_id IS NOT NULL
  GROUP BY fa1.actor_id, fa2.actor_id
  ORDER BY amount DESC
    LIMIT 1;

SELECT a1.first_name || ' ' || a1.last_name AS first_actor, a2.first_name || ' ' || a2.last_name AS second_actor , film.title
FROM film
  INNER JOIN film_actor fa1 ON film.film_id = fa1.film_id
  INNER JOIN actor a1 ON fa1.actor_id = a1.actor_id
  INNER JOIN film_actor fa2 ON film.film_id = fa2.film_id
  INNER JOIN actor a2 ON fa2.actor_id = a2.actor_id
WHERE fa1.actor_id = (SELECT a1 FROM top_actors) AND fa2.actor_id = (SELECT a2 FROM top_actors)
ORDER BY film.title
