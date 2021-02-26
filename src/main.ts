import { NestFactory } from '@nestjs/core';
import { AppModule } from './app.module';
import { NestExpressApplication } from '@nestjs/platform-express';
import { join } from 'path';
import * as session from 'express-session';
import { ConfigService } from '@nestjs/config';
import * as expressHandlebars from 'express-handlebars';

async function bootstrap() {
  const app = await NestFactory.create<NestExpressApplication>(AppModule);
  app.engine('handlebars', expressHandlebars());
  app.setViewEngine('handlebars');
  app.useStaticAssets(join(__dirname, '..', 'public'));
  app.setBaseViewsDir(join(__dirname, '..', 'views'));

  const configService = app.get<ConfigService>(ConfigService);

  app.use(
    session({
      secret: configService.get<string>('APP_SECRET'),
      resave: false,
      saveUninitialized: false,
    }),
  );

  app.enableShutdownHooks();

  await app.listen(configService.get<number>('PORT'));
}
bootstrap();
