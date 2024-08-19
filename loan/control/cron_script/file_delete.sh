## namofunding loan.xfund.co.kr/data/files
find /home/namo/loan/partner/data/file -ctime +100 -exec rm -f {} \;
find /home/namo/loan/partner/data/log/loan_* -ctime +30 -exec rm -f {} \;
