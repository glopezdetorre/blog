FROM nginx

RUN rm /etc/nginx/conf.d/default.conf
ADD ./config/devel/nginx.conf /etc/nginx/conf.d/nginx.conf
