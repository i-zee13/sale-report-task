#Clone the repositry 
    git clone <repository-url>
    cd sales-report
#Insatall the composer 
       composer install
#Configure environment
    cp .env.example .env
    php artisan key:generate
#Configure database
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=sales_report
     DB_USERNAME=root
     DB_PASSWORD=
#run migration with seeder
    php artisan migrate:fresh --seed
#serve the project 


#Artisan Commands 
    php artisan report:sales 2024-01-01 2024-4-30 //for cli
//for Json
    GET /sales-report-json?start_date=2024-01-01&end_date=2024-4-30  
//for CSV
    GET /sales-report-csv?start_date=2024-01-01&end_date=2024-4-30 
//for Test case 
  php artisan test --filter=SalesReportTest
                                           

