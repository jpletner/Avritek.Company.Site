
; <<>> DiG 9.3.6-P1-RedHat-9.3.6-16.P1.el5_7.1 <<>> @192.52.178.30 avr-recycling.com
; (1 server found)
;; global options:  printcmd
;; Got answer:
;; ->>HEADER<<- opcode: QUERY, status: NOERROR, id: 27267
;; flags: qr rd; QUERY: 1, ANSWER: 0, AUTHORITY: 2, ADDITIONAL: 2

;; QUESTION SECTION:
;avr-recycling.com.		IN	A

;; AUTHORITY SECTION:
avr-recycling.com.	172800	IN	NS	ns2815.hostgator.com.
avr-recycling.com.	172800	IN	NS	ns2816.hostgator.com.

;; ADDITIONAL SECTION:
ns2815.hostgator.com.	172800	IN	A	50.22.104.153
ns2816.hostgator.com.	172800	IN	A	50.22.113.32

;; Query time: 129 msec
;; SERVER: 192.52.178.30#53(192.52.178.30)
;; WHEN: Sat Feb 18 01:32:56 2012
;; MSG SIZE  rcvd: 119

