CREATE
TEMPORARY TABLE total
SELECT issued.customer_oid, issued.offer_oid, issued.offer_status
FROM insly_offer issued
WHERE issued.offer_status = 11
  AND issued.offer_type = 'renovation';

CREATE TEMPORARY TABLE total2
SELECT * FROM total;

INSERT INTO total
SELECT approved.customer_oid, approved.offer_oid, approved.offer_status
FROM (SELECT approved.customer_oid,
             approved.offer_oid,
             approved.offer_status,
             approved.approved_date,
             @row_num :=IF(@current_customer=approved.customer_oid ,@row_num+1,1)    AS row_num,
    @current_customer := approved.customer_oid as current_customer
      FROM approved
      ORDER BY approved.customer_oid ASC, approved.approved_date DESC) approved
WHERE row_num = 1
  AND approved.customer_oid NOT IN (SELECT customer_oid FROM total2);
