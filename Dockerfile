# extend the swoole dockerfile
FROM xlight/docker-php7-swoole

# Copy the local package files to the container's workspace
ADD . /workdir

ENV DOCKER 1
ENV PORT 9501

# Set the working directory to avoid relative paths after this
WORKDIR /workdir

# Run the command by default when the container starts
ENTRYPOINT php server.php start

# Document that the service runs on port 9501
EXPOSE 9501
