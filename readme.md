# Restrict Content Pro expired trial users remmover 
WordPress plugin which removes RCP expired trial users excluding affiliates.

## Description
A LearnDash + Restrict Content Pro based website had more than 30k users, about 1/3 of them were expired trial users that were polluting several tables with a lot of data which caused MySQL queries to run noticeably slower. The problem was that there were some affiliate users that had money on their accounts. Solution was to collect affiliate's IDs, write them to the database and the exclude when removing. 