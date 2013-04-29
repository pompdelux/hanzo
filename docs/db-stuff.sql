SET FOREIGN_KEY_CHECKS = 0;
  truncate table products;
  truncate table products_domains_prices;
  truncate table products_i18n;
  truncate table products_images;
  truncate table products_images_categories_sort;
  truncate table products_images_product_references;
  truncate table products_stock;
  truncate table products_to_categories;
  truncate table products_washing_instructions;
SET FOREIGN_KEY_CHECKS = 1;

