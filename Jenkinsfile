pipeline {
  agent any
  stages {

    stage('Build') {
      steps {
        sh 'mkdir silverstripe-cache'
        sh 'composer require --prefer-dist --no-update silverstripe-themes/simple:~3.2'
        sh 'composer update --no-suggest --prefer-dist'
      }
    }

    stage('PHPUnit') {
      steps {
        sh 'vendor/bin/phpunit --coverage-clover=build/logs/clover.xml --log-junit=build/logs/junit.xml --coverage-xml=build/logs/coverage'
      }
    }

    stage('Cleanup') {
      steps {
        sh 'rm -rf silverstripe-cache'
      }
    }
  }
}
