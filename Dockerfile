# extend the swoole dockerfile
FROM twosee/swoole-coroutine:latest

# Copy the local package files to the container's workspace
ADD . /workdir

ENV DOCKER 1
ENV PORT 8080
ENV HOST=0.0.0.0
ENV HOSTNAME=docker.local

# Set the working directory to avoid relative paths after this
WORKDIR /workdir

# Run the command by default when the container starts
ENTRYPOINT php slim.php start

# Document that the service runs on port 8080
EXPOSE 8080
