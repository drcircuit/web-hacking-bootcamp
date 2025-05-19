# Use a Schema 2-compatible base image with Java 8
FROM eclipse-temurin:8-jre

# Set environment variables
ENV TOMCAT_VERSION=8.5.0
ENV TOMCAT_HOME=/usr/local/tomcat

# Install dependencies
RUN apt-get update && apt-get install -y \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Download and install Tomcat 8.5.0
RUN curl -O https://archive.apache.org/dist/tomcat/tomcat-8/v8.5.0/bin/apache-tomcat-8.5.0.tar.gz \
    && mkdir -p $TOMCAT_HOME \
    && tar -xzf apache-tomcat-8.5.0.tar.gz -C $TOMCAT_HOME --strip-components=1 \
    && rm apache-tomcat-8.5.0.tar.gz

# Expose default Tomcat port
EXPOSE 8080

# Start Tomcat
COPY web.xml /usr/local/tomcat/conf/web.xml
COPY index.jsp /usr/local/tomcat/webapps/ROOT/index.jsp
COPY flag.txt /flag.txt

CMD ["sh", "/usr/local/tomcat/bin/catalina.sh", "run"]
